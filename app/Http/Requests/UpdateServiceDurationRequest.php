<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceDurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'duration_type' => 'required|in:hour,day,week',
            'duration_value' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }
}
