<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        // Map category names to translation keys
        $categoryMap = [
            'الحساب' => 'account',
            'عام' => 'general',
            'الخدمات' => 'services',
            'الدفع' => 'payment',
            'تقني' => 'technical',
            'الحجوزات' => 'bookings',
            'Account' => 'account',
            'General' => 'general',
            'Services' => 'services',
            'Payment' => 'payment',
            'Technical' => 'technical',
            'Bookings' => 'bookings',
        ];

        // Translate category names
        $translatedFaqs = [];
        foreach ($faqs as $category => $items) {
            $categoryKey = $categoryMap[$category] ?? $category;
            $translatedCategory = __('messages.' . $categoryKey);
            
            // If translation returns the key itself (translation not found), use original category
            if ($translatedCategory === 'messages.' . $categoryKey) {
                $translatedCategory = $category;
            }
            
            $translatedFaqs[$translatedCategory] = $items;
        }

        return view('faqs.index', compact('translatedFaqs'));
    }
}
