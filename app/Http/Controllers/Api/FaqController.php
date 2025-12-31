<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of active FAQs.
     */
    public function index()
    {
        $faqs = Faq::where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return response()->json([
            'success' => true,
            'data' => $faqs
        ]);
    }

    /**
     * Display a listing of FAQs by category.
     */
    public function getByCategory($category)
    {
        $faqs = Faq::where('is_active', true)
            ->where('category', $category)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $faqs
        ]);
    }
}
