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
        Schema::table('service_points_pricing', function (Blueprint $table) {
            // Add subscription_id column
            $table->foreignId('subscription_id')->nullable()->after('consultation_id')->constrained('subscriptions')->onDelete('cascade');
            
            // Update item_type enum to include 'subscription'
            DB::statement("ALTER TABLE service_points_pricing MODIFY COLUMN item_type ENUM('service', 'consultation', 'subscription')");
            
            // Add index for subscription_id
            $table->index(['subscription_id', 'item_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_points_pricing', function (Blueprint $table) {
            // Remove index
            $table->dropIndex(['subscription_id', 'item_type']);
            
            // Remove subscription_id column
            $table->dropForeign(['subscription_id']);
            $table->dropColumn('subscription_id');
            
            // Revert item_type enum
            DB::statement("ALTER TABLE service_points_pricing MODIFY COLUMN item_type ENUM('service', 'consultation')");
        });
    }
};
