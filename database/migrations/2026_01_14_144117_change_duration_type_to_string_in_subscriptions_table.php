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
        // Change duration_type from enum to string
        DB::statement("ALTER TABLE subscriptions MODIFY duration_type VARCHAR(20) NOT NULL DEFAULT 'month'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to enum
        DB::statement("ALTER TABLE subscriptions MODIFY duration_type ENUM('month', 'year', 'lifetime') NOT NULL DEFAULT 'month'");
    }
};
