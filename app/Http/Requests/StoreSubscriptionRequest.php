<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_type' => 'required|in:monthly,3months,6months,yearly',
            'features' => 'required|array|min:1',
            'features.*.name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
