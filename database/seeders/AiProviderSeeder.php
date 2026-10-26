<?php

namespace Database\Seeders;

use App\Models\AiProvider;
use Illuminate\Database\Seeder;

class AiProviderSeeder extends Seeder
{
    public function run(): void
    {
        AiProvider::updateOrCreate(
            ['key' => 'openai'],
            [
                'name' => 'OpenAI',
                'capabilities' => ['script_generation'],
                'is_enabled' => true,
            ],
        );
    }
}
