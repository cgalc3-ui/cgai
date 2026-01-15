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
        Schema::create('ai_videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();
            $table->string('video_url');
            $table->string('thumbnail')->nullable();
            $table->integer('duration')->nullable()->comment('Duration in seconds');
            $table->integer('views_count')->default(0);
            $table->foreignId('category_id')->nullable()->constrained('ai_service_categories')->onDelete('set null');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index('is_featured');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_videos');
    }
};
