<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Translatable;

class ReadyAppCategory extends Model
{
    use Translatable;

    protected $fillable = [
        'name',
        'name_en',
        'slug',
        'description',
        'description_en',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });
    }

    /**
     * Get all apps in this category
     */
    public function apps(): HasMany
    {
        return $this->hasMany(ReadyApp::class, 'category_id');
    }

    /**
     * Get active apps in this category
     */
    public function activeApps(): HasMany
    {
        return $this->hasMany(ReadyApp::class, 'category_id')->where('is_active', true);
    }
}
