<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sub_category_id' => 'required|exists:sub_categories,id',
            'specialization_id' => 'nullable|exists:specializations,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug',
            'description' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }
}
