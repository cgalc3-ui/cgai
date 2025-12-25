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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // vendor_registered, order_created, verification_code, etc.
            $table->string('entity_type')->nullable(); // App\Models\Order, App\Models\User, etc.
            $table->unsignedBigInteger('entity_id')->nullable(); // ID of the related entity
            $table->string('phone'); // Recipient phone number
            $table->text('message'); // SMS message content
            $table->string('status')->default('pending'); // pending, sent, failed, delivered
            $table->string('provider')->default('fourjawaly'); // SMS provider used
            $table->string('provider_message_id')->nullable(); // Provider's message ID
            $table->text('provider_response')->nullable(); // Full provider response
            $table->text('error_message')->nullable(); // Error details if failed
            $table->integer('attempt')->default(1); // Attempt number
            $table->string('hash')->unique(); // Unique hash for idempotency
            $table->timestamp('sent_at')->nullable(); // When SMS was actually sent
            $table->timestamp('delivered_at')->nullable(); // When SMS was delivered (if available)
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['event_type', 'entity_type', 'entity_id']);
            $table->index(['phone', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index('hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
