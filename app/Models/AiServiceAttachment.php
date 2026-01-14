<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiServiceAttachment extends Model
{
    protected $fillable = [
        'ai_service_request_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'order',
    ];

    protected $casts = [
        'ai_service_request_id' => 'integer',
        'file_size' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the request that owns this attachment
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(AiServiceRequest::class, 'ai_service_request_id');
    }
}
