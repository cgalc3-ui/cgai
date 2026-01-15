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
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:500',
            'features_en' => 'nullable|array',
            'features_en.*' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'duration_type' => 'required|string|in:month,year,lifetime',
            'ai_enabled' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_pro' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        // Ensure numeric fields are properly formatted
        if ($this->has('price')) {
            $this->merge([
                'price' => (float) $this->price
            ]);
        }
        
        // Filter out empty features
        if ($this->has('features') && is_array($this->features)) {
            $this->merge([
                'features' => array_values(array_filter($this->features, function($feature) {
                    return !empty(trim($feature ?? ''));
                }))
            ]);
        }
        
        if ($this->has('features_en') && is_array($this->features_en)) {
            $this->merge([
                'features_en' => array_values(array_filter($this->features_en, function($feature) {
                    return !empty(trim($feature ?? ''));
                }))
            ]);
        }
    }
}
