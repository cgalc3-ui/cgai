<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify enum to include 'processing'
        DB::statement("ALTER TABLE `ready_app_orders` MODIFY COLUMN `status` ENUM('pending', 'processing', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum (without processing)
        DB::statement("ALTER TABLE `ready_app_orders` MODIFY COLUMN `status` ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending'");
    }
};
