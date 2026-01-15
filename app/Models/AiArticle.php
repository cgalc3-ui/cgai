<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Translatable;

class AiArticle extends Model
{
    use Translatable;

    protected $fillable = [
        'title',
        'title_en',
        'slug',
        'content',
        'content_en',
        'excerpt',
        'excerpt_en',
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

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = \Illuminate\Support\Str::slug($article->title);
            }
        });

        static::updating(function ($article) {
            if ($article->isDirty('title') && empty($article->slug)) {
                $article->slug = \Illuminate\Support\Str::slug($article->title);
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
     * Scope for active articles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured articles
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for published articles
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
