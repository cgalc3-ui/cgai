<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class HomeServicesSection extends Model
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
     */
    public function categories()
    {
        if (!$this->category_ids || empty($this->category_ids)) {
            return collect([]);
        }
        
        return Category::whereIn('id', $this->category_ids)
            ->where('is_active', true)
            ->orderByRaw('FIELD(id, ' . implode(',', $this->category_ids) . ')')
            ->get();
    }
}

