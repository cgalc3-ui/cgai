<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'service_duration_id' => 'required|exists:service_durations,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => 'يجب اختيار الخدمة',
            'service_id.exists' => 'الخدمة المختارة غير موجودة',
            'service_duration_id.required' => 'يجب اختيار مدة الخدمة',
            'service_duration_id.exists' => 'مدة الخدمة المختارة غير موجودة',
            'booking_date.required' => 'يجب اختيار تاريخ الحجز',
            'booking_date.date' => 'تاريخ الحجز غير صحيح',
            'booking_date.after_or_equal' => 'تاريخ الحجز يجب أن يكون اليوم أو بعد اليوم',
            'start_time.required' => 'يجب اختيار وقت البدء',
            'start_time.date_format' => 'وقت البدء غير صحيح',
        ];
    }
}
