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
        Schema::create('home_services_sections', function (Blueprint $table) {
            $table->id();
            $table->string('heading')->nullable(); // العنوان الرئيسي
            $table->string('heading_en')->nullable();
            $table->text('description')->nullable(); // النص الوصفي
            $table->text('description_en')->nullable();
            $table->json('category_ids')->nullable(); // IDs للفئات المحددة
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_services_sections');
    }
};

