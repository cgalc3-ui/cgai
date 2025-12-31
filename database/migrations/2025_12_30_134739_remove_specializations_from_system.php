<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * إزالة specialization_id من services
     * إزالة جداول specializations و employee_specialization
     */
    public function up(): void
    {
        // إزالة foreign key constraint أولاً
        if (Schema::hasColumn('services', 'specialization_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropForeign(['specialization_id']);
                $table->dropColumn('specialization_id');
            });
        }

        // إزالة جداول specializations
        Schema::dropIfExists('employee_specialization');
        Schema::dropIfExists('specializations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إعادة إنشاء جدول specializations
        Schema::create('specializations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // إعادة إنشاء جدول employee_specialization
        Schema::create('employee_specialization', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('specialization_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['employee_id', 'specialization_id']);
        });

        // إعادة إضافة specialization_id إلى services
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('specialization_id')->nullable()->constrained()->onDelete('set null')->after('sub_category_id');
        });
    }
};
