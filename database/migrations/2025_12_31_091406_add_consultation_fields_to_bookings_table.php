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
        Schema::table('bookings', function (Blueprint $table) {
            // جعل service_id nullable لأن الحجز قد يكون لاستشارة
            $table->foreignId('service_id')->nullable()->change();
            
            // إضافة consultation_id
            $table->foreignId('consultation_id')->nullable()->after('service_id')
                ->constrained('consultations')->onDelete('cascade');
            
            // إضافة type لتحديد نوع الحجز
            $table->enum('booking_type', ['service', 'consultation'])->default('service')
                ->after('consultation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['consultation_id']);
            $table->dropColumn(['consultation_id', 'booking_type']);
            $table->foreignId('service_id')->nullable(false)->change();
        });
    }
};
