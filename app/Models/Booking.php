<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'customer_id',
        'employee_id',
        'service_id',
        'consultation_id',
        'booking_type',
        'time_slot_id',
        'booking_date',
        'start_time',
        'end_time',
        'total_price',
        'status',
        'payment_status',
        'payment_method',
        'points_used',
        'points_price',
        'notes',
        'payment_id',
        'payment_data',
        'paid_at',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'total_price' => 'decimal:2',
        'payment_data' => 'array',
        'paid_at' => 'datetime',
        'booking_type' => 'string',
    ];

    /**
     * Get the customer that made the booking
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the employee for this booking
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the service for this booking
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the consultation for this booking
     */
    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    /**
     * Get the rating for this booking
     */
    public function rating(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Rating::class);
    }

    /**
     * Get the bookable item (service or consultation)
     */
    public function getBookableAttribute()
    {
        if ($this->booking_type === 'consultation') {
            return $this->consultation;
        }
        return $this->service;
    }

    /**
     * Get the time slot for this booking
     */
    /**
     * Get the time slot for this booking (primary)
     */
    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    /**
     * Get all time slots for this booking
     */
    public function timeSlots(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(TimeSlot::class);
    }

    public function getFormattedDurationAttribute(): string
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        $diffInMinutes = $end->diffInMinutes($start);

        if ($diffInMinutes < 60) {
            return $diffInMinutes . ' ' . ($diffInMinutes == 1 ? __('messages.minute') : __('messages.minutes'));
        }

        $hours = floor($diffInMinutes / 60);
        $minutes = $diffInMinutes % 60;

        $hourText = $hours == 1 ? __('messages.hour') : __('messages.hours');
        $result = $hours . ' ' . $hourText;
        
        if ($minutes > 0) {
            $minuteText = $minutes == 1 ? __('messages.minute') : __('messages.minutes');
            $result .= ' ' . __('messages.and') . ' ' . $minutes . ' ' . $minuteText;
        }

        return $result;
    }

    /**
     * Get the actual status based on current time
     * Returns: 'pending', 'in_progress', 'completed', 'cancelled'
     */
    public function getActualStatusAttribute(): string
    {
        // If cancelled, return cancelled
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }

        $now = \Carbon\Carbon::now();
        $bookingDateTime = \Carbon\Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->start_time);
        $endDateTime = \Carbon\Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->end_time);

        // If booking has ended
        if ($now->gte($endDateTime)) {
            return 'completed';
        }

        // If booking has started but not ended
        if ($now->gte($bookingDateTime) && $now->lt($endDateTime)) {
            return 'in_progress';
        }

        // If booking hasn't started yet, return 'pending' (قيد الانتظار)
        return 'pending';
    }

    /**
     * Get time remaining until booking starts (in minutes)
     * Returns null if booking has already started
     */
    public function getTimeUntilStartAttribute(): ?int
    {
        $now = \Carbon\Carbon::now();
        $bookingDateTime = \Carbon\Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->start_time);

        if ($now->gte($bookingDateTime)) {
            return null; // Booking has already started
        }

        return $now->diffInMinutes($bookingDateTime);
    }

    /**
     * Get elapsed time since booking started (in minutes)
     * Returns null if booking hasn't started yet
     */
    public function getElapsedTimeAttribute(): ?int
    {
        $now = \Carbon\Carbon::now();
        $bookingDateTime = \Carbon\Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->start_time);
        $endDateTime = \Carbon\Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->end_time);

        if ($now->lt($bookingDateTime)) {
            return null; // Booking hasn't started yet
        }

        if ($now->gte($endDateTime)) {
            // Booking has ended, return total duration
            return $bookingDateTime->diffInMinutes($endDateTime);
        }

        return $bookingDateTime->diffInMinutes($now);
    }

    /**
     * Get remaining time until booking ends (in minutes)
     * Returns null if booking hasn't started or has ended
     */
    public function getTimeUntilEndAttribute(): ?int
    {
        $now = \Carbon\Carbon::now();
        $bookingDateTime = \Carbon\Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->start_time);
        $endDateTime = \Carbon\Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->end_time);

        if ($now->lt($bookingDateTime)) {
            return null; // Booking hasn't started yet
        }

        if ($now->gte($endDateTime)) {
            return null; // Booking has ended
        }

        return $now->diffInMinutes($endDateTime);
    }

    /**
     * Format time remaining/elapsed for display
     */
    public function getTimeDisplayAttribute(): ?array
    {
        $actualStatus = $this->actual_status;

        if ($actualStatus === 'completed') {
            return [
                'type' => 'completed',
                'message' => 'تم الانتهاء',
                'time' => null
            ];
        }

        if ($actualStatus === 'in_progress') {
            $elapsed = $this->elapsed_time;
            $remaining = $this->time_until_end;

            return [
                'type' => 'in_progress',
                'message' => 'قيد التنفيذ',
                'elapsed_minutes' => $elapsed,
                'elapsed_formatted' => $this->formatMinutes($elapsed),
                'remaining_minutes' => $remaining,
                'remaining_formatted' => $remaining ? $this->formatMinutes($remaining) : null
            ];
        }

        // Pending or confirmed - show time until start
        $timeUntilStart = $this->time_until_start;
        if ($timeUntilStart !== null) {
            return [
                'type' => 'upcoming',
                'message' => 'قادم',
                'minutes' => $timeUntilStart,
                'formatted' => $this->formatMinutes($timeUntilStart)
            ];
        }

        return null;
    }

    /**
     * Format minutes to human readable string
     */
    private function formatMinutes(int $minutes): string
    {
        if ($minutes < 60) {
            $minuteText = $minutes == 1 ? __('messages.minute') : __('messages.minutes');
            return $minutes . ' ' . $minuteText;
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        $hourText = $hours == 1 ? __('messages.hour') : __('messages.hours');
        $result = $hours . ' ' . $hourText;
        
        if ($mins > 0) {
            $minuteText = $mins == 1 ? __('messages.minute') : __('messages.minutes');
            $result .= ' ' . __('messages.and') . ' ' . $mins . ' ' . $minuteText;
        }

        return $result;
    }

    /**
     * Get formatted time slots display (combines consecutive slots)
     */
    public function getFormattedTimeSlotsAttribute(): array
    {
        if (!$this->timeSlots || $this->timeSlots->isEmpty()) {
            return [
                [
                    'start' => $this->start_time,
                    'end' => $this->end_time,
                    'is_range' => false,
                ]
            ];
        }

        $sortedSlots = $this->timeSlots->sortBy('start_time')->values();
        $ranges = [];
        $currentRange = null;

        foreach ($sortedSlots as $slot) {
            $slotStart = \Carbon\Carbon::parse($slot->start_time);
            $slotEnd = \Carbon\Carbon::parse($slot->end_time);

            if ($currentRange === null) {
                // Start new range
                $currentRange = [
                    'start' => $slot->start_time,
                    'end' => $slot->end_time,
                    'slots' => [$slot],
                ];
            } else {
                $currentEnd = \Carbon\Carbon::parse($currentRange['end']);

                // Check if this slot is consecutive (end of current range equals start of this slot)
                if ($currentEnd->format('H:i:s') === $slotStart->format('H:i:s')) {
                    // Extend current range
                    $currentRange['end'] = $slot->end_time;
                    $currentRange['slots'][] = $slot;
                } else {
                    // Save current range and start new one
                    $ranges[] = [
                        'start' => $currentRange['start'],
                        'end' => $currentRange['end'],
                        'is_range' => count($currentRange['slots']) > 1,
                    ];
                    $currentRange = [
                        'start' => $slot->start_time,
                        'end' => $slot->end_time,
                        'slots' => [$slot],
                    ];
                }
            }
        }

        // Add last range
        if ($currentRange !== null) {
            $ranges[] = [
                'start' => $currentRange['start'],
                'end' => $currentRange['end'],
                'is_range' => count($currentRange['slots']) > 1,
            ];
        }

        return $ranges;
    }

    /**
     * Update status automatically based on time
     * This should be called periodically (e.g., via scheduled task)
     */
    public function updateStatusAutomatically(): bool
    {
        $actualStatus = $this->actual_status;
        $currentStatus = $this->status;

        // Don't update if cancelled
        if ($currentStatus === 'cancelled') {
            return false;
        }

        // Update to in_progress if started
        if ($actualStatus === 'in_progress' && $currentStatus !== 'in_progress') {
            $this->status = 'in_progress';
            $this->save();
            return true;
        }

        // Update to completed if ended
        if ($actualStatus === 'completed' && $currentStatus !== 'completed') {
            $this->status = 'completed';
            $this->save();
            return true;
        }

        return false;
    }
}
