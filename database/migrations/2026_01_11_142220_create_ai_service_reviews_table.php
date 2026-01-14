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
        Schema::create('ai_service_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('ai_service_id')->constrained('ai_services')->onDelete('cascade');
            $table->integer('rating')->unsigned(); // 1-5
            $table->text('comment')->nullable();
            $table->text('comment_en')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            // Ensure a user can only review a service once
            $table->unique(['user_id', 'ai_service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_service_reviews');
    }
};
