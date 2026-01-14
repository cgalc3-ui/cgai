<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Translatable;

class ReadyApp extends Model
{
    use Translatable;

    protected $fillable = [
        'category_id',
        'name',
        'name_en',
        'slug',
        'short_description',
        'short_description_en',
        'description',
        'description_en',
        'full_description',
        'full_description_en',
        'price',
        'original_price',
        'currency',
        'video_url',
        'video_thumbnail',
        'specifications',
        'tags',
        'rating',
        'reviews_count',
        'views_count',
        'purchases_count',
        'favorites_count',
        'is_popular',
        'is_new',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'specifications' => 'array',
        'tags' => 'array',
        'rating' => 'decimal:2',
        'reviews_count' => 'integer',
        'views_count' => 'integer',
        'purchases_count' => 'integer',
        'favorites_count' => 'integer',
        'is_popular' => 'boolean',
        'is_new' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($app) {
            if (empty($app->slug)) {
                $app->slug = \Illuminate\Support\Str::slug($app->name);
            }
        });

        static::updating(function ($app) {
            if ($app->isDirty('name') && empty($app->slug)) {
                $app->slug = \Illuminate\Support\Str::slug($app->name);
            }
        });
    }

    /**
     * Get the category that owns this app
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ReadyAppCategory::class, 'category_id');
    }

    /**
     * Get all images for this app
     */
    public function images(): HasMany
    {
        return $this->hasMany(ReadyAppImage::class)->orderBy('order');
    }

    /**
     * Get main image
     */
    public function mainImage(): HasMany
    {
        return $this->hasMany(ReadyAppImage::class)->where('type', 'main')->orderBy('order')->limit(1);
    }

    /**
     * Get gallery images
     */
    public function galleryImages(): HasMany
    {
        return $this->hasMany(ReadyAppImage::class)->where('type', 'gallery')->orderBy('order');
    }

    /**
     * Get all features for this app
     */
    public function features(): HasMany
    {
        return $this->hasMany(ReadyAppFeature::class)->orderBy('order');
    }

    /**
     * Get all screenshots for this app
     */
    public function screenshots(): HasMany
    {
        return $this->hasMany(ReadyAppScreenshot::class)->orderBy('order');
    }

    /**
     * Get all orders for this app
     */
    public function orders(): HasMany
    {
        return $this->hasMany(ReadyAppOrder::class);
    }

    /**
     * Get all ratings/reviews for this app
     */
    public function reviews()
    {
        return $this->morphMany(Rating::class, 'ratable')->where('is_approved', true);
    }

    /**
     * Get all favorites for this app
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(ReadyAppFavorite::class);
    }

    /**
     * Get users who favorited this app
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ready_app_favorites')
            ->withTimestamps();
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute(): ?float
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100, 2);
        }
        return null;
    }

    /**
     * Get main image URL
     */
    public function getMainImageUrlAttribute(): ?string
    {
        $mainImage = $this->images()->where('type', 'main')->first();
        return $mainImage ? $mainImage->url : null;
    }
}
