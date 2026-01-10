<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'consultation_id' => 'required|exists:consultations,id',
            'booking_date' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'time_slot_id' => 'required|exists:time_slots,id',
            'notes' => 'nullable|string',
            'payment_method' => 'nullable|string|in:online,points',
        ];
    }

    public function messages(): array
    {
        return [
            'consultation_id.required' => 'يجب اختيار الاستشارة',
            'consultation_id.exists' => 'الاستشارة المختارة غير موجودة',
            'booking_date.required' => 'يجب تحديد تاريخ الحجز',
            'time_slot_id.required' => 'يجب اختيار وقت الحجز',
            'time_slot_id.exists' => 'الوقت المختار غير موجود',
        ];
    }
}
