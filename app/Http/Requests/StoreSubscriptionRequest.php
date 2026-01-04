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
            'duration_type' => 'required|in:month,year,lifetime',
            'max_debtors' => 'required|integer|min:0',
            'max_messages' => 'required|integer|min:0',
            'ai_enabled' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }
}
