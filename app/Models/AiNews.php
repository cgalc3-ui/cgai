<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Translatable;

class AiNews extends Model
{
    use Translatable;

    protected $fillable = [
        'title',
        'title_en',
        'slug',
        'content',
        'content_en',
        'image',
        'author_id',
        'category_id',
        'views_count',
        'is_featured',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'author_id' => 'integer',
        'category_id' => 'integer',
        'views_count' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = \Illuminate\Support\Str::slug($news->title);
            }
        });

        static::updating(function ($news) {
            if ($news->isDirty('title') && empty($news->slug)) {
                $news->slug = \Illuminate\Support\Str::slug($news->title);
            }
        });
    }

    /**
     * Get the author
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AiServiceCategory::class, 'category_id');
    }

    /**
     * Scope for active news
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured news
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for published news
     */
    public function scopePublished($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }
}
