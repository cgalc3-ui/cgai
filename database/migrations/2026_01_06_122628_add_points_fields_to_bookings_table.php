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
            $table->enum('payment_method', ['cash', 'points'])->default('cash')->after('payment_status');
            $table->decimal('points_used', 15, 2)->nullable()->after('payment_method');
            $table->decimal('points_price', 15, 2)->nullable()->after('points_used'); // سعر الخدمة بالنقاط وقت الحجز
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'points_used', 'points_price']);
        });
    }
};
