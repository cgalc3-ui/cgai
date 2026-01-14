<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    protected $fillable = [
        'ratable_id',
        'ratable_type',
        'booking_id',
        'customer_id',
        'rating',
        'comment',
        'is_approved',
    ];

    protected $casts = [
        'ratable_id' => 'integer',
        'rating' => 'integer',
        'is_approved' => 'boolean',
    ];

    /**
     * Get the parent ratable model (Service, Consultation, AiService, or ReadyApp).
     */
    public function ratable()
    {
        return $this->morphTo();
    }

    /**
     * Get the booking that was rated (optional)
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the customer who made the rating
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
