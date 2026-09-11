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
        Schema::create('media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('storage_location_id')->nullable();
            $table->string('filename');
            $table->string('original_name');
            $table->enum('media_type', ['image', 'video', 'audio']);
            $table->bigInteger('size');
            $table->string('path');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('storage_location_id')->references('id')->on('storage_locations')->onDelete('set null');
            
            $table->index('media_type');
            $table->index('user_id');
            $table->index('storage_location_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
