<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_providers', function (Blueprint $table): void {
            $table->json('services')->nullable()->after('name');
            $table->string('default_base_url', 2048)->nullable()->default('')->after('services');
            $table->string('auth_type', 64)->default('api-key')->after('default_base_url');
            $table->json('credential_fields')->nullable()->after('auth_type');
            $table->string('credential_header', 255)->nullable()->after('credential_fields');
            $table->string('api_key_url', 2048)->nullable()->after('credential_header');
        });
    }

    public function down(): void
    {
        Schema::table('ai_providers', function (Blueprint $table): void {
            $table->dropColumn([
                'services',
                'default_base_url',
                'auth_type',
                'credential_fields',
                'credential_header',
                'api_key_url',
            ]);
        });
    }
};
