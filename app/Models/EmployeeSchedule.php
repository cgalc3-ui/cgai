<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSchedule extends Model
{
    protected $fillable = [
        'employee_id',
        'days_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the employee that owns this schedule
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get days of week as array
     */
    public function getDaysOfWeekArrayAttribute(): array
    {
        if (is_string($this->days_of_week)) {
            $decoded = json_decode($this->days_of_week, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($this->days_of_week) ? $this->days_of_week : [];
    }

    /**
     * Set days of week as JSON
     */
    public function setDaysOfWeekAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['days_of_week'] = json_encode($value);
        } else {
            $this->attributes['days_of_week'] = $value;
        }
    }

    /**
     * Get formatted days names
     */
    public function getDaysNamesAttribute(): string
    {
        $days = [
            0 => 'الأحد',
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
        ];

        $selectedDays = $this->days_of_week_array;
        $names = array_map(function($day) use ($days) {
            return $days[$day] ?? '';
        }, $selectedDays);

        return implode('، ', $names);
    }
}
