<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $consultationId = $this->route('consultation')->id;

        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('consultations', 'slug')->ignore($consultationId)],
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'fixed_price' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }
}
