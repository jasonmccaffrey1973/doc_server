<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<UserActivity>
     */
    protected $model = UserActivity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'activity_type' => $this->faker->randomElement(['page_view', 'login', 'logout']),
            'path' => $this->faker->url(),
            'route_name' => $this->faker->word(),
            'method' => $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'metadata' => [
                'browser' => $this->faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge']),
                'os' => $this->faker->randomElement(['Windows', 'macOS', 'Linux']),
                'device_type' => $this->faker->randomElement(['Desktop', 'Mobile', 'Tablet']),
            ],
            'response_status' => $this->faker->randomElement([200, 201, 400, 404, 500]),
            'response_time_ms' => $this->faker->numberBetween(10, 5000),
        ];
    }
}
