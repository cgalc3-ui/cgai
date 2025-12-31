<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\Translatable;

class Category extends Model
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
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }

    public function activeSubCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class)->where('is_active', true);
    }

    /**
     * Get all employees with this category (specialization)
     */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_category');
    }

    /**
     * Get all consultations for this category
     */
    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    /**
     * Get active consultations
     */
    public function activeConsultations(): HasMany
    {
        return $this->hasMany(Consultation::class)->where('is_active', true);
    }
}
