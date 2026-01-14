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
        Schema::create('footers', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable(); // شعار الشركة
            $table->string('logo_en')->nullable();
            $table->text('description')->nullable(); // وصف الشركة
            $table->text('description_en')->nullable();
            $table->string('email')->nullable(); // البريد الإلكتروني
            $table->string('phone')->nullable(); // رقم الهاتف
            $table->string('working_hours')->nullable(); // ساعات العمل
            $table->string('working_hours_en')->nullable();
            $table->json('quick_links')->nullable(); // الروابط السريعة [{title, title_en, link}]
            $table->json('content_links')->nullable(); // روابط المحتوى [{title, title_en, link}]
            $table->json('support_links')->nullable(); // روابط الدعم والمساعدة [{title, title_en, link}]
            $table->json('social_media')->nullable(); // روابط التواصل الاجتماعي [{platform, url, icon}]
            $table->string('copyright_text')->nullable(); // نص حقوق النشر
            $table->string('copyright_text_en')->nullable();
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
        Schema::dropIfExists('footers');
    }
};
