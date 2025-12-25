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
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->string('phone'); // Phone number to verify
            $table->string('code'); // Verification code
            $table->string('type')->default('registration'); // registration, login, password_reset, etc.
            $table->integer('attempts')->default(0); // Number of verification attempts
            $table->boolean('verified')->default(false); // Whether code has been verified
            $table->timestamp('expires_at'); // Expiration time
            $table->timestamp('verified_at')->nullable(); // When code was verified
            $table->timestamps();
            
            // Indexes
            $table->index(['phone', 'type', 'verified']);
            $table->index(['phone', 'code']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_codes');
    }
};
