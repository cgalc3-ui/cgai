<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization will be handled in controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_id' => 'nullable|exists:bookings,id',
            'ratable_id' => 'nullable|integer',
            'ratable_type' => 'nullable|string|in:service,consultation,ai_service,ready_app',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'booking_id.required' => 'معرف الحجز مطلوب',
            'booking_id.exists' => 'الحجز المحدد غير موجود',
            'rating.required' => 'التقييم مطلوب',
            'rating.integer' => 'التقييم يجب أن يكون رقماً',
            'rating.min' => 'التقييم يجب أن يكون على الأقل 1',
            'rating.max' => 'التقييم يجب أن يكون على الأكثر 5',
            'comment.max' => 'التعليق يجب أن يكون أقل من 1000 حرف',
        ];
    }
}
