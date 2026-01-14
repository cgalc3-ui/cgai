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
        // Check if table exists
        if (Schema::hasTable('home_services_sections')) {
            // Rename title to heading if exists
            if (Schema::hasColumn('home_services_sections', 'title')) {
                Schema::table('home_services_sections', function (Blueprint $table) {
                    $table->renameColumn('title', 'heading');
                });
            }
            
            // Rename title_en to heading_en if exists
            if (Schema::hasColumn('home_services_sections', 'title_en')) {
                Schema::table('home_services_sections', function (Blueprint $table) {
                    $table->renameColumn('title_en', 'heading_en');
                });
            }
            
            // Add category_ids if not exists
            if (!Schema::hasColumn('home_services_sections', 'category_ids')) {
                Schema::table('home_services_sections', function (Blueprint $table) {
                    $table->json('category_ids')->nullable()->after('description_en');
                });
            }
            
            // Add is_active if not exists
            if (!Schema::hasColumn('home_services_sections', 'is_active')) {
                Schema::table('home_services_sections', function (Blueprint $table) {
                    $table->boolean('is_active')->default(true)->after('category_ids');
                    $table->index('is_active');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('home_services_sections')) {
            // Reverse the changes
            if (Schema::hasColumn('home_services_sections', 'heading')) {
                Schema::table('home_services_sections', function (Blueprint $table) {
                    $table->renameColumn('heading', 'title');
                });
            }
            
            if (Schema::hasColumn('home_services_sections', 'heading_en')) {
                Schema::table('home_services_sections', function (Blueprint $table) {
                    $table->renameColumn('heading_en', 'title_en');
                });
            }
            
            if (Schema::hasColumn('home_services_sections', 'category_ids')) {
                Schema::table('home_services_sections', function (Blueprint $table) {
                    $table->dropColumn('category_ids');
                });
            }
            
            if (Schema::hasColumn('home_services_sections', 'is_active')) {
                Schema::table('home_services_sections', function (Blueprint $table) {
                    $table->dropIndex(['is_active']);
                    $table->dropColumn('is_active');
                });
            }
        }
    }
};
