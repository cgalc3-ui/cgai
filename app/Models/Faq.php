<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Translatable;

class Faq extends Model
{
    use Translatable;

    protected $fillable = [
        'question',
        'question_en',
        'answer',
        'answer_en',
        'category',
        'category_en',
        'is_active',
        'sort_order',
    ];
}
