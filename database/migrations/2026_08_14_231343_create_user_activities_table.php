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
        Schema::create('user_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('activity_type'); // 'page_view', 'login', 'logout', etc.
            $table->string('path')->nullable(); // URL path for page views
            $table->string('route_name')->nullable(); // Laravel route name
            $table->string('method')->nullable(); // HTTP method (GET, POST, etc.)
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable(); // Additional data like browser, OS, device info
            $table->integer('response_status')->nullable(); // HTTP response status
            $table->integer('response_time_ms')->nullable(); // Response time in milliseconds
            $table->timestamps();

            // Indexes for common queries
            $table->index('user_id');
            $table->index('activity_type');
            $table->index('created_at');
            $table->index(['user_id', 'activity_type']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
