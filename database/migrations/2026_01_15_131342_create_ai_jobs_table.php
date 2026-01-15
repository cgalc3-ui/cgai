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
        Schema::create('ai_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('description_en')->nullable();
            $table->string('company');
            $table->string('company_en')->nullable();
            $table->string('location');
            $table->string('location_en')->nullable();
            $table->string('salary_range')->nullable();
            $table->enum('job_type', ['full_time', 'part_time', 'contract', 'freelance', 'internship'])->default('full_time');
            $table->text('requirements')->nullable();
            $table->text('requirements_en')->nullable();
            $table->text('benefits')->nullable();
            $table->text('benefits_en')->nullable();
            $table->string('application_email')->nullable();
            $table->string('application_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('is_featured');
            $table->index('job_type');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_jobs');
    }
};
