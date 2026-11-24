<?php

namespace Database\Seeders;

use App\Models\StorageLocation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create a single deterministic test user (optional). Commented out to avoid duplicate email issues.
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'username' => 'testuser',
        //     'email' => 'test@example.com',
        // ]);

        // Seed 200 random users
        User::factory(200)->create();

        StorageLocation::firstOrCreate(
            ['name' => 'Default Local Storage'],
            [
                'type' => 'local',
                'is_default' => true,
                'configuration' => null,
            ]
        );

        $this->call(AiProviderSeeder::class);
    }
}
