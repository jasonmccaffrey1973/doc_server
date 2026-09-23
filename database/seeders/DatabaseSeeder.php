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

        User::factory()->create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);

        StorageLocation::firstOrCreate(
            ['name' => 'Default Local Storage'],
            [
                'type' => 'local',
                'is_default' => true,
                'configuration' => null,
            ]
        );
    }
}
