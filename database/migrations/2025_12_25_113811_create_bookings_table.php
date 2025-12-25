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
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('employee_id')->constrained()->onDelete('cascade');
                $table->foreignId('service_id')->constrained()->onDelete('cascade');
                $table->foreignId('service_duration_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('time_slot_id')->constrained()->onDelete('cascade');
                $table->date('booking_date');
                $table->time('start_time');
                $table->time('end_time');
                $table->decimal('total_price', 10, 2);
                $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
                $table->enum('payment_status', ['paid', 'unpaid', 'refunded'])->default('unpaid');
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['customer_id', 'booking_date']);
                $table->index(['employee_id', 'booking_date']);
                $table->index(['status', 'payment_status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
