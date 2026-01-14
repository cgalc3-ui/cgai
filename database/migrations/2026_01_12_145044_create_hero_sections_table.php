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
        Schema::create('hero_sections', function (Blueprint $table) {
            $table->id();
            $table->string('heading')->nullable(); // العنوان الرئيسي
            $table->string('heading_en')->nullable();
            $table->string('subheading')->nullable(); // العنوان الثانوي
            $table->string('subheading_en')->nullable();
            $table->text('description')->nullable(); // النص الوصفي
            $table->text('description_en')->nullable();
            $table->string('background_image')->nullable(); // صورة الخلفية
            $table->json('buttons')->nullable(); // الأزرار (JSON array)
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
        Schema::dropIfExists('hero_sections');
    }
};
