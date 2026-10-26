<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_provider_connections', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('ai_provider_id')->constrained('ai_providers')->cascadeOnDelete();
            $table->string('label')->nullable();
            // Encrypted by Laravel's encrypted:array model cast; ciphertext is not queryable.
            $table->text('credentials');
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['user_id', 'ai_provider_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_provider_connections');
    }
};
