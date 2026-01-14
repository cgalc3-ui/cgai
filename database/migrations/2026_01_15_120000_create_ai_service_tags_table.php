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
        Schema::create('ai_service_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم التقنية (مثل: ChatGPT, Grok, Claude AI)
            $table->string('name_en')->nullable();
            $table->string('slug')->unique(); // للاستخدام في URLs
            $table->string('icon')->nullable(); // اسم الأيقونة (مثل: fas fa-bolt)
            $table->string('color')->nullable(); // لون التقنية (hex code)
            $table->integer('sort_order')->default(0); // ترتيب العرض
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_service_tags');
    }
};

