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
            'booking_date' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'time_slot_ids' => 'required|array|min:1', // يجب أن يكون مصفوفة وعنصر واحد على الأقل
            'time_slot_ids.*' => 'exists:time_slots,id', // تحقق من أن كل معرف في المصفوفة موجود
            'notes' => 'nullable|string',
            'payment_method' => 'nullable|string|in:online,points',
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => 'يجب اختيار الخدمة',
            'service_id.exists' => 'الخدمة المختارة غير موجودة',
            'booking_date.required' => 'تاريخ الحجز مطلوب',
            'booking_date.date' => 'تاريخ الحجز غير صالح',
            'booking_date.date_format' => 'تاريخ الحجز يجب أن يكون بصيغة Y-m-d',
            'booking_date.after_or_equal' => 'تاريخ الحجز يجب أن يكون اليوم أو في المستقبل',
            'time_slot_ids.required' => 'يجب اختيار وقت الحجز',
            'time_slot_ids.array' => 'يجب أن يكون وقت الحجز مصفوفة',
            'time_slot_ids.min' => 'يجب أن يكون وقت الحجز مصفوفة وعنصر واحد على الأقل',
            'time_slot_ids.exists' => 'يجب أن يكون وقت الحجز موجود في القاعدة',
            'notes.string' => 'الملاحظات يجب أن تكون نص',
            'notes.max' => 'الملاحظات لا يمكن أن تتجاوز 1000 حرف',
            'payment_method.in' => 'طريقة الدفع غير صالحة. الطرق المتاحة: online, points',
        ];
    }
}
