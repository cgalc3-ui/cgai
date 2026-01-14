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
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_type'); // 'logo', 'menu_item', 'button'
            $table->string('item_key')->nullable(); // مفتاح فريد للعنصر (مثل: 'home', 'services', 'login_btn')
            $table->string('title')->nullable(); // نص العنصر
            $table->string('title_en')->nullable();
            $table->string('link')->nullable(); // الرابط
            $table->string('icon')->nullable(); // أيقونة FontAwesome
            $table->string('image')->nullable(); // صورة (للوجو مثلاً)
            $table->string('target')->default('_self'); // _self, _blank
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['item_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
    }
};
