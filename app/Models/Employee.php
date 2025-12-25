<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'specialization',
        'bio',
        'hourly_rate',
        'is_available',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    /**
     * Get the user that owns the employee
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all time slots for this employee
     */
    public function timeSlots(): HasMany
    {
        return $this->hasMany(TimeSlot::class);
    }

    /**
     * Get all bookings for this employee
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get available time slots for a specific date
     */
    public function getAvailableTimeSlots($date)
    {
        return $this->timeSlots()
            ->where('date', $date)
            ->where('is_available', true)
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Get all specializations for this employee
     */
    public function specializations(): BelongsToMany
    {
        return $this->belongsToMany(Specialization::class, 'employee_specialization');
    }

    /**
     * Check if employee is available for a specific time slot
     */
    public function isAvailableForTimeSlot($timeSlotId, $date, $startTime, $endTime): bool
    {
        if (!$this->is_available) {
            return false;
        }

        $hasConflictingBooking = $this->bookings()
            ->where('booking_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where(function ($q1) use ($startTime, $endTime) {
                        $q1->where('start_time', '<=', $startTime)
                           ->where('end_time', '>', $startTime);
                    })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '<', $endTime)
                           ->where('end_time', '>=', $endTime);
                    })
                    ->orWhere(function ($q3) use ($startTime, $endTime) {
                        $q3->where('start_time', '>=', $startTime)
                           ->where('end_time', '<=', $endTime);
                    });
                });
            })
            ->exists();

        return !$hasConflictingBooking;
    }

    /**
     * Find available employee for a specialization and time slot
     */
    public static function findAvailableForSpecializationAndTimeSlot($specializationId, $timeSlotId, $date, $startTime, $endTime)
    {
        $timeSlot = \App\Models\TimeSlot::find($timeSlotId);
        
        if (!$timeSlot || !$timeSlot->is_available) {
            return null;
        }

        $employees = static::where('is_available', true)
            ->whereHas('specializations', function ($query) use ($specializationId) {
                $query->where('specializations.id', $specializationId);
            })
            ->get();

        foreach ($employees as $employee) {
            $hasTimeSlot = $employee->timeSlots()
                ->where('id', $timeSlotId)
                ->exists();
            
            if ($hasTimeSlot && $employee->isAvailableForTimeSlot($timeSlotId, $date, $startTime, $endTime)) {
                return $employee;
            }
        }

        return null;
    }
}
