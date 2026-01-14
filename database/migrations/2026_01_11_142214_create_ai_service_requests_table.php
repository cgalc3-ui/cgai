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
        Schema::create('ai_service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('ai_service_categories')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->text('use_case');
            $table->json('expected_features')->nullable();
            $table->enum('budget_range', ['low', 'medium', 'high', 'custom'])->default('medium');
            $table->decimal('custom_budget', 10, 2)->nullable();
            $table->enum('urgency', ['low', 'medium', 'high'])->default('medium');
            $table->date('deadline')->nullable();
            $table->enum('status', ['pending', 'reviewing', 'quoted', 'approved', 'in_progress', 'completed', 'cancelled', 'rejected'])->default('pending');
            $table->decimal('estimated_price', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('SAR');
            $table->enum('contact_preference', ['phone', 'email', 'both'])->default('both');
            $table->text('admin_notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('quoted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_service_requests');
    }
};
