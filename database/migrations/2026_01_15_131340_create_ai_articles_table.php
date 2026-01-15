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
        Schema::create('ai_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('slug')->unique();
            $table->text('content');
            $table->text('content_en')->nullable();
            $table->text('excerpt')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('ai_service_categories')->onDelete('set null');
            $table->integer('views_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('is_featured');
            $table->index('published_at');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_articles');
    }
};
