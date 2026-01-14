<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;
use App\Models\ReadyAppCategory;

class HomeReadyAppsSection extends Model
{
    use Translatable;

    protected $fillable = [
        'heading',
        'heading_en',
        'description',
        'description_en',
        'category_ids',
        'is_active',
    ];

    protected $casts = [
        'category_ids' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get categories for this section
     * If category_ids is empty, returns all active categories
     */
    public function categories()
    {
        if (!$this->category_ids || empty($this->category_ids)) {
            // Return all active categories if no specific categories are selected
            return ReadyAppCategory::where('is_active', true)
                ->orderBy('name')
                ->get();
        }
        
        return ReadyAppCategory::whereIn('id', $this->category_ids)
            ->where('is_active', true)
            ->orderByRaw('FIELD(id, ' . implode(',', $this->category_ids) . ')')
            ->get();
    }
}

