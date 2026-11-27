<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('users and usersMeta search is case-insensitive', function () {
    User::factory()->create([
        'name' => 'Pat Smith',
        'username' => 'pat_smith',
        'email' => 'pat@example.com',
    ]);

    User::factory()->create([
        'name' => 'Alice Jones',
        'username' => 'alice_j',
        'email' => 'alice@example.com',
    ]);

    $query = '
        query Users($search: String) {
            users(first: 10, search: $search) {
                edges {
                    node {
                        id
                        name
                        email
                    }
                }
            }
            usersMeta(search: $search) {
                totalCount
                filteredCount
            }
        }
    ';

    $response = $this->postJson('/graphql', [
        'query' => $query,
        'variables' => ['search' => 'PAT'],
    ]);

    $response->assertOk()
        ->assertJsonCount(1, 'data.users.edges')
        ->assertJsonPath('data.users.edges.0.node.name', 'Pat Smith')
        ->assertJsonPath('data.usersMeta.totalCount', 2)
        ->assertJsonPath('data.usersMeta.filteredCount', 1);
});
