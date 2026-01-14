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
        Schema::create('ai_service_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_service_request_id')->constrained('ai_service_requests')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->enum('file_type', ['image', 'document', 'video', 'other'])->default('document');
            $table->integer('file_size')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_service_attachments');
    }
};
