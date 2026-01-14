<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Translatable;

class AiServiceScreenshot extends Model
{
    use Translatable;

    protected $fillable = [
        'ai_service_id',
        'url',
        'title',
        'title_en',
        'order',
    ];

    protected $casts = [
        'ai_service_id' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the service that owns this screenshot
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(AiService::class, 'ai_service_id');
    }
}
