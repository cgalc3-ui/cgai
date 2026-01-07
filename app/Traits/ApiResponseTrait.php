<?php

namespace App\Traits;

trait ApiResponseTrait
{
    /**
     * Filter data to return only locale-specific columns
     * Removes _en columns for Arabic and removes non-_en columns for English
     */
    protected function filterLocaleColumns($data)
    {
        $locale = app()->getLocale();
        
        // Handle Collections
        if ($data instanceof \Illuminate\Support\Collection) {
            return $data->map(function ($item) use ($locale) {
                return $this->filterArray($item->toArray(), $locale);
            })->values();
        }
        
        if (is_array($data)) {
            return $this->filterArray($data, $locale);
        } elseif (is_object($data) && method_exists($data, 'toArray')) {
            $array = $data->toArray();
            return $this->filterArray($array, $locale);
        }
        
        return $data;
    }
    
    /**
     * Filter array recursively
     */
    private function filterArray($array, $locale)
    {
        if (!is_array($array)) {
            return $array;
        }
        
        $filtered = [];
        
        foreach ($array as $key => $value) {
            // Skip _en columns for Arabic
            if ($locale === 'ar' && str_ends_with($key, '_en')) {
                continue;
            }
            
            // Skip non-_en columns for English (only for name, description, question, answer, category, title, content, subject)
            if ($locale === 'en') {
                $baseKey = str_replace('_en', '', $key);
                if (in_array($baseKey, ['name', 'description', 'question', 'answer', 'category', 'title', 'content', 'subject']) && !str_ends_with($key, '_en')) {
                    // Check if _en version exists
                    $enKey = $baseKey . '_en';
                    if (isset($array[$enKey])) {
                        continue; // Skip the non-_en version if _en exists
                    }
                }
            }
            
            // Recursively filter nested arrays/objects
            if (is_array($value)) {
                $filtered[$key] = $this->filterArray($value, $locale);
            } elseif (is_object($value) && method_exists($value, 'toArray')) {
                $filtered[$key] = $this->filterArray($value->toArray(), $locale);
            } else {
                $filtered[$key] = $value;
            }
        }
        
        return $filtered;
    }
}



