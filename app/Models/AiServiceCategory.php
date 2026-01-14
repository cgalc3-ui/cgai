<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Translatable;

class AiServiceCategory extends Model
{
    use Translatable;

    protected $fillable = [
        'name',
        'name_en',
        'slug',
        'description',
        'description_en',
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
     * Get all services in this category
     */
    public function services(): HasMany
    {
        return $this->hasMany(AiService::class, 'category_id');
    }

    /**
     * Get active services in this category
     */
    public function activeServices(): HasMany
    {
        return $this->hasMany(AiService::class, 'category_id')->where('is_active', true);
    }

    /**
     * Get all requests in this category
     */
    public function requests(): HasMany
    {
        return $this->hasMany(AiServiceRequest::class, 'category_id');
    }
}
