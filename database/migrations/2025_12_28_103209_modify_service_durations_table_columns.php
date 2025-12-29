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
        // Change duration_type to string (VARCHAR)
        DB::statement("ALTER TABLE service_durations MODIFY duration_type VARCHAR(255) NOT NULL");

        // Change duration_value to decimal to support fractional hours
        DB::statement("ALTER TABLE service_durations MODIFY duration_value DECIMAL(10, 2) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert duration_value to integer (Warning: data loss for decimals)
        DB::statement("ALTER TABLE service_durations MODIFY duration_value INT NOT NULL");

        // Revert duration_type to enum (Warning: data loss for 'custom')
        // We can't easily revert to exact enum without potentially losing data if 'custom' exists
        // So this is a best-effort revert
        DB::statement("ALTER TABLE service_durations MODIFY duration_type ENUM('hour', 'day', 'week') NOT NULL");
    }
};
