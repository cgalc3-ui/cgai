<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Translatable;

class ReadyAppImage extends Model
{
    use Translatable;

    protected $fillable = [
        'ready_app_id',
        'url',
        'type',
        'alt',
        'alt_en',
        'order',
    ];

    protected $casts = [
        'ready_app_id' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the app that owns this image
     */
    public function app(): BelongsTo
    {
        return $this->belongsTo(ReadyApp::class, 'ready_app_id');
    }
}
