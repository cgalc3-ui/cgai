<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Remove foreign key and column from bookings
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['service_duration_id']);
            $table->dropColumn('service_duration_id');
        });

        // 2. Drop service_durations table
        Schema::dropIfExists('service_durations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('service_durations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('duration_type');
            $table->float('duration_value');
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('service_duration_id')->nullable()->constrained()->onDelete('set null');
        });
    }
};
