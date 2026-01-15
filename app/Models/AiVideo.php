<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Translatable;

class AiVideo extends Model
{
    use Translatable;

    protected $fillable = [
        'title',
        'title_en',
        'slug',
        'description',
        'description_en',
        'video_url',
        'thumbnail',
        'duration',
        'views_count',
        'category_id',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'duration' => 'integer',
        'views_count' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($video) {
            if (empty($video->slug)) {
                $video->slug = \Illuminate\Support\Str::slug($video->title);
            }
        });

        static::updating(function ($video) {
            if ($video->isDirty('title') && empty($video->slug)) {
                $video->slug = \Illuminate\Support\Str::slug($video->title);
            }
        });
    }

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AiServiceCategory::class, 'category_id');
    }

    /**
     * Scope for active videos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured videos
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration) {
            return '';
        }

        $hours = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%d:%02d', $minutes, $seconds);
    }
}
