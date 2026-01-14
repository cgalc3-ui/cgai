<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadyAppFavorite extends Model
{
    protected $fillable = [
        'user_id',
        'ready_app_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'ready_app_id' => 'integer',
    ];

    /**
     * Get the user who favorited this app
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the app that was favorited
     */
    public function app(): BelongsTo
    {
        return $this->belongsTo(ReadyApp::class, 'ready_app_id');
    }
}
