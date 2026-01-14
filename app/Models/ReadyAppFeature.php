<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Translatable;

class ReadyAppFeature extends Model
{
    use Translatable;

    protected $fillable = [
        'ready_app_id',
        'title',
        'title_en',
        'icon',
        'order',
    ];

    protected $casts = [
        'ready_app_id' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the app that owns this feature
     */
    public function app(): BelongsTo
    {
        return $this->belongsTo(ReadyApp::class, 'ready_app_id');
    }
}
