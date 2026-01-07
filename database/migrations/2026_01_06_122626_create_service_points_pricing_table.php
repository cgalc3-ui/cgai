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
        Schema::create('service_points_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            $table->foreignId('consultation_id')->nullable()->constrained('consultations')->onDelete('cascade');
            $table->enum('item_type', ['service', 'consultation']); // نوع العنصر
            $table->decimal('points_price', 15, 2); // سعر النقاط
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['service_id', 'item_type']);
            $table->index(['consultation_id', 'item_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_points_pricing');
    }
};
