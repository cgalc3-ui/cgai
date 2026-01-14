<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Translatable;

class AiServiceReview extends Model
{
    use Translatable;

    protected $fillable = [
        'user_id',
        'ai_service_id',
        'rating',
        'comment',
        'comment_en',
        'is_approved',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'ai_service_id' => 'integer',
        'rating' => 'integer',
        'is_approved' => 'boolean',
    ];

    /**
     * Get the user who wrote this review
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service being reviewed
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(AiService::class, 'ai_service_id');
    }
}
