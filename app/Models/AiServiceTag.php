<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class AiServiceTag extends Model
{
    use Translatable;

    protected $fillable = [
        'name',
        'name_en',
        'slug',
        'icon',
        'image',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = \Illuminate\Support\Str::slug($tag->name);
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && empty($tag->slug)) {
                $tag->slug = \Illuminate\Support\Str::slug($tag->name);
            }
        });
    }

    /**
     * Get services that have this tag
     */
    public function services()
    {
        return AiService::where('is_active', true)
            ->where(function ($query) {
                $query->whereJsonContains('tags', $this->name);
                if ($this->name_en) {
                    $query->orWhereJsonContains('tags', $this->name_en);
                }
            })
            ->get();
    }

    /**
     * Get services count
     */
    public function getServicesCountAttribute()
    {
        return AiService::where('is_active', true)
            ->where(function ($query) {
                $query->whereJsonContains('tags', $this->name);
                if ($this->name_en) {
                    $query->orWhereJsonContains('tags', $this->name_en);
                }
            })
            ->count();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}

