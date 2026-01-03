<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class HelpGuide extends Model
{
    use Translatable;

    protected $fillable = [
        'role',
        'title',
        'title_en',
        'content',
        'content_en',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope to filter by role
     */
    public function scopeForRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope to get active guides
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }
}
