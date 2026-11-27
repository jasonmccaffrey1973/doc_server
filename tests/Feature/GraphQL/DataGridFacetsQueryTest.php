<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dataGridFacets returns distinct options with counts and excludes each facet own filter', function () {
    User::factory()->create(['name' => 'Pat Smith', 'username' => 'pat1', 'email' => 'pat1@example.com']);
    User::factory()->create(['name' => 'Pat Smith', 'username' => 'pat2', 'email' => 'pat2@example.com']);
    User::factory()->create(['name' => 'Alice Jones', 'username' => 'alice', 'email' => 'alice@example.com']);

    $query = '
        query DataGridFacets(
            $fields: [String!]!
            $search: String
            $filters: [DataGridFilterInput!]
        ) {
            dataGridFacets(fields: $fields, search: $search, filters: $filters) {
                field
                totalCount
                hasMore
                options {
                    value
                    label
                    count
                }
            }
        }
    ';

    // Filtering by name="Pat Smith" should still return both "Pat Smith" and "Alice Jones" for the name facet,
    // while narrowing the username facet to only pat1 and pat2.
    $response = $this->postJson('/graphql', [
        'query' => $query,
        'variables' => [
            'fields' => ['name', 'username'],
            'filters' => [
                ['field' => 'name', 'value' => 'Pat Smith'],
            ],
        ],
    ]);

    $response->assertOk()
        ->assertJsonPath('data.dataGridFacets.0.field', 'name')
        ->assertJsonPath('data.dataGridFacets.0.totalCount', 2)
        ->assertJsonPath('data.dataGridFacets.0.options.0.value', 'Pat Smith')
        ->assertJsonPath('data.dataGridFacets.0.options.0.count', 2)
        ->assertJsonPath('data.dataGridFacets.1.field', 'username')
        ->assertJsonPath('data.dataGridFacets.1.totalCount', 2);
});

test('dataGridFacets supports facetSearch and paging', function () {
    User::factory()->create(['name' => 'Pat Alpha', 'username' => 'pat_a', 'email' => 'a@example.com']);
    User::factory()->create(['name' => 'Pat Beta', 'username' => 'pat_b', 'email' => 'b@example.com']);
    User::factory()->create(['name' => 'Alice Gamma', 'username' => 'alice_g', 'email' => 'c@example.com']);

    $query = '
        query DataGridFacets($fields: [String!]!, $facetSearch: String, $limit: Int, $offset: Int) {
            dataGridFacets(fields: $fields, facetSearch: $facetSearch, limit: $limit, offset: $offset) {
                field
                totalCount
                hasMore
                options {
                    value
                    count
                }
            }
        }
    ';

    $response = $this->postJson('/graphql', [
        'query' => $query,
        'variables' => [
            'fields' => ['name'],
            'facetSearch' => 'pat',
            'limit' => 1,
            'offset' => 0,
        ],
    ]);

    $response->assertOk()
        ->assertJsonPath('data.dataGridFacets.0.totalCount', 2)
        ->assertJsonPath('data.dataGridFacets.0.hasMore', true)
        ->assertJsonCount(1, 'data.dataGridFacets.0.options');
});

test('dataGridFacets rejects disallowed fields', function () {
    $query = '
        query DataGridFacets($fields: [String!]!) {
            dataGridFacets(fields: $fields) {
                field
                options { value count }
            }
        }
    ';

    $response = $this->postJson('/graphql', [
        'query' => $query,
        'variables' => [
            'fields' => ['password'],
        ],
    ]);

    $response->assertOk()
        ->assertJsonStructure(['errors']);
});
