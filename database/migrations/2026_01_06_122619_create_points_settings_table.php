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
        Schema::create('points_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('points_per_riyal', 10, 2)->default(10.00); // 1 ريال = 10 نقاط
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default settings
        \Illuminate\Support\Facades\DB::table('points_settings')->insert([
            'points_per_riyal' => 10.00,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points_settings');
    }
};
