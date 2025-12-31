<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $serviceId = $this->route('service')->id;

        return [
            'sub_category_id' => 'required|exists:sub_categories,id',
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('services', 'slug')->ignore($serviceId)],
            'description' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }
}
