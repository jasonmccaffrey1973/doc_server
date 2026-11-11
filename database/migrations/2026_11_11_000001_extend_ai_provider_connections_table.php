<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_provider_connections', function (Blueprint $table): void {
            $table->dropUnique('ai_provider_connections_user_id_ai_provider_id_unique');
            $table->string('service')->default('text');
            $table->string('base_url', 2048)->default('');
            $table->string('auth_type')->default('api-key');
            $table->json('model_ids')->nullable();
            $table->string('status')->default('not-tested');
            $table->index(['user_id', 'ai_provider_id']);
        });
    }

    public function down(): void
    {
        Schema::table('ai_provider_connections', function (Blueprint $table): void {
            $table->dropIndex('ai_provider_connections_user_id_ai_provider_id_index');
            $table->dropColumn(['service', 'base_url', 'auth_type', 'model_ids', 'status']);
            $table->unique(['user_id', 'ai_provider_id']);
        });
    }
};
