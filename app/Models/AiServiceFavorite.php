<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiServiceFavorite extends Model
{
    protected $fillable = [
        'user_id',
        'ai_service_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'ai_service_id' => 'integer',
    ];

    /**
     * Get the user who favorited this service
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service that was favorited
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(AiService::class, 'ai_service_id');
    }
}
