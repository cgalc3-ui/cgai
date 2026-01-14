<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class NavigationItem extends Model
{
    use Translatable;

    protected $fillable = [
        'item_type',
        'title',
        'title_en',
        'link',
        'icon',
        'image',
        'target',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('item_type', $type);
    }

    // Helper methods
    public function isLogo()
    {
        return $this->item_type === 'logo';
    }

    public function isMenuItem()
    {
        return $this->item_type === 'menu_item';
    }

    public function isButton()
    {
        return $this->item_type === 'button';
    }
}
