<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_providers', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('key')->unique();
            $table->string('name');
            $table->json('capabilities')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        DB::table('ai_providers')->insert([
            'id' => (string) Str::uuid(),
            'key' => 'openai',
            'name' => 'OpenAI',
            'capabilities' => json_encode(['script_generation']),
            'is_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_providers');
    }
};
