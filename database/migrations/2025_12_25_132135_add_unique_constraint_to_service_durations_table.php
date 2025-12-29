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
        if (Schema::hasTable('service_durations')) {
            try {
                Schema::table('service_durations', function (Blueprint $table) {
                    // Add unique constraint on service_id, duration_type, and duration_value
                    $table->unique(['service_id', 'duration_type', 'duration_value'], 'service_durations_service_id_duration_type_duration_value_unique');
                });
            } catch (\Exception $e) {
                // Constraint might already exist, ignore the error
                if (strpos($e->getMessage(), 'Duplicate key name') === false) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('service_durations')) {
            Schema::table('service_durations', function (Blueprint $table) {
                $table->dropUnique('service_durations_service_id_duration_type_duration_value_unique');
            });
        }
    }
};
