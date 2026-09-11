<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('storage_locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->enum('type', ['local', 's3', 'gcs'])->default('local');
            $table->json('configuration')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Create index on is_default for quick lookup
        Schema::table('storage_locations', function (Blueprint $table) {
            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_locations');
    }
};
