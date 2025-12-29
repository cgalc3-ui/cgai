<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change status and payment_status to string (VARCHAR) to avoid ENUM text truncated issues
        DB::statement("ALTER TABLE bookings MODIFY status VARCHAR(50) NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE bookings MODIFY payment_status VARCHAR(50) NOT NULL DEFAULT 'unpaid'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to ENUM (Best effort)
        DB::statement("ALTER TABLE bookings MODIFY status ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE bookings MODIFY payment_status ENUM('paid', 'unpaid', 'refunded') NOT NULL DEFAULT 'unpaid'");
    }
};
