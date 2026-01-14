<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // تحديد دور المستخدم
        // إذا كان الأدمن، يمكنه اختيار الدور من الـ request
        if ($user && $user->isAdmin() && $request->has('role')) {
            $role = $request->get('role');
            // التحقق من أن الدور صحيح
            if (!in_array($role, ['customer', 'staff', 'admin'])) {
                $role = 'admin';
            }
        } else {
            // تحديد الدور حسب المستخدم
            $role = 'customer';
            if ($user) {
                if ($user->isAdmin()) {
                    $role = 'admin';
                } elseif ($user->isStaff()) {
                    $role = 'staff';
                }
            }
        }
        
        $faqs = Faq::where('is_active', true)
            ->where('role', $role)
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

        // Translate category names and group by translated category
        $translatedFaqs = [];
        foreach ($faqs as $category => $items) {
            // Get translated category name
            $categoryKey = $categoryMap[$category] ?? $category;
            $translatedCategory = __('messages.' . $categoryKey);
            
            // If translation returns the key itself (translation not found), use trans() method
            if ($translatedCategory === 'messages.' . $categoryKey) {
                // Try to get translated category from first item
                $firstItem = $items->first();
                if ($firstItem) {
                    $translatedCategory = $firstItem->trans('category') ?: $category;
                } else {
                    $translatedCategory = $category;
                }
            }
            
            // Filter items to only include those with translated content
            $filteredItems = $items->filter(function($faq) {
                return $faq->trans('question') && $faq->trans('answer');
            });
            
            if ($filteredItems->isNotEmpty()) {
                $translatedFaqs[$translatedCategory] = $filteredItems;
            }
        }

        return view('faqs.index', compact('translatedFaqs', 'role', 'user'));
    }
}
