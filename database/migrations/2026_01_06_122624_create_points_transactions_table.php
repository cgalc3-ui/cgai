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
        Schema::create('points_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained('wallets')->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null');
            $table->enum('type', ['purchase', 'usage', 'refund', 'adjustment']); // شراء، استخدام، استرجاع، تعديل يدوي
            $table->decimal('points', 15, 2); // عدد النقاط (موجب للشراء/الاسترجاع، سالب للاستخدام)
            $table->decimal('amount_paid', 10, 2)->nullable(); // المبلغ المدفوع (للشراء فقط)
            $table->string('payment_id')->nullable(); // معرف الدفع
            $table->text('description')->nullable();
            $table->decimal('balance_before', 15, 2); // الرصيد قبل العملية
            $table->decimal('balance_after', 15, 2); // الرصيد بعد العملية
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->index('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points_transactions');
    }
};
