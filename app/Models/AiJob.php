<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class AiJob extends Model
{
    use Translatable;

    protected $fillable = [
        'title',
        'title_en',
        'slug',
        'description',
        'description_en',
        'company',
        'company_en',
        'location',
        'location_en',
        'salary_range',
        'job_type',
        'requirements',
        'requirements_en',
        'benefits',
        'benefits_en',
        'application_email',
        'application_url',
        'is_featured',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($job) {
            if (empty($job->slug)) {
                $job->slug = \Illuminate\Support\Str::slug($job->title);
            }
        });

        static::updating(function ($job) {
            if ($job->isDirty('title') && empty($job->slug)) {
                $job->slug = \Illuminate\Support\Str::slug($job->title);
            }
        });
    }

    /**
     * Scope for active jobs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope for featured jobs
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Check if job is expired
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
