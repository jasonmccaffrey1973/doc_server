<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserActivityTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_view_is_logged(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/');

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $user->id,
            'activity_type' => 'page_view',
            'method' => 'GET',
        ]);
    }

    public function test_user_activity_has_user_relationship(): void
    {
        $user = User::factory()->create();
        $activity = UserActivity::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($activity->user->is($user));
    }

    public function test_user_can_retrieve_activities(): void
    {
        $user = User::factory()->create();
        UserActivity::factory(3)->create(['user_id' => $user->id, 'activity_type' => 'page_view']);

        $activities = $user->activities()->get();

        $this->assertCount(3, $activities);
    }

    public function test_activities_can_be_filtered_by_type(): void
    {
        $user = User::factory()->create();
        UserActivity::factory(2)->create(['user_id' => $user->id, 'activity_type' => 'page_view']);
        UserActivity::factory(1)->create(['user_id' => $user->id, 'activity_type' => 'login']);

        $pageViews = UserActivity::byType('page_view')->count();
        $logins = UserActivity::byType('login')->count();

        $this->assertSame(2, $pageViews);
        $this->assertSame(1, $logins);
    }

    public function test_activities_have_metadata(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/');

        $activity = UserActivity::where('user_id', $user->id)->first();

        $this->assertNotNull($activity->metadata);
        $this->assertArrayHasKey('user_agent_string', $activity->metadata);
        $this->assertArrayHasKey('device_type', $activity->metadata);
    }
}
