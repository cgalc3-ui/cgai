<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Translatable;

class ReadyAppReview extends Model
{
    use Translatable;

    protected $fillable = [
        'user_id',
        'ready_app_id',
        'rating',
        'comment',
        'comment_en',
        'is_approved',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'ready_app_id' => 'integer',
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
     * Get the app being reviewed
     */
    public function app(): BelongsTo
    {
        return $this->belongsTo(ReadyApp::class, 'ready_app_id');
    }
}
