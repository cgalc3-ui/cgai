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
        Schema::create('ai_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('ai_service_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->text('short_description_en')->nullable();
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();
            $table->text('full_description')->nullable();
            $table->text('full_description_en')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('SAR');
            $table->string('video_url')->nullable();
            $table->string('video_thumbnail')->nullable();
            $table->json('specifications')->nullable();
            $table->json('tags')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->integer('purchases_count')->default(0);
            $table->integer('favorites_count')->default(0);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_services');
    }
};
