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
        Schema::create('ready_app_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ready_app_id')->constrained('ready_apps')->onDelete('cascade');
            $table->string('url');
            $table->enum('type', ['main', 'gallery'])->default('gallery');
            $table->string('alt')->nullable();
            $table->string('alt_en')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ready_app_images');
    }
};
