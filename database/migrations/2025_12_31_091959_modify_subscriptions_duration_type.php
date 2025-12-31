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
        // Modify enum to support new duration types
        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN duration_type ENUM('monthly', '3months', '6months', 'yearly') DEFAULT 'monthly'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old enum values
        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN duration_type ENUM('month', 'year', 'lifetime') DEFAULT 'month'");
    }
};
