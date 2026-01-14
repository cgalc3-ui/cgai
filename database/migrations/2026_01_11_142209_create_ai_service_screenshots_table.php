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
        Schema::create('ai_service_screenshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_service_id')->constrained('ai_services')->onDelete('cascade');
            $table->string('url');
            $table->string('title')->nullable();
            $table->string('title_en')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_service_screenshots');
    }
};
