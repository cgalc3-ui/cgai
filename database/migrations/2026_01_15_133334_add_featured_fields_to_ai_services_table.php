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
        Schema::table('ai_services', function (Blueprint $table) {
            $table->boolean('is_latest')->default(false)->after('is_featured')->comment('Show in latest technologies section');
            $table->boolean('is_best_of_month')->default(false)->after('is_latest')->comment('Show in best technologies of the month section');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_services', function (Blueprint $table) {
            $table->dropColumn(['is_latest', 'is_best_of_month']);
        });
    }
};
