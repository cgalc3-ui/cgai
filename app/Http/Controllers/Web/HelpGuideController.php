<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\HelpGuide;
use Illuminate\Http\Request;

class HelpGuideController extends Controller
{
    /**
     * Display help and guide page based on user role
     */
    public function index()
    {
        $user = auth()->user();
        
        // تحديد نوع المستخدم
        $role = 'customer';
        if ($user->isAdmin()) {
            $role = 'admin';
        } elseif ($user->isStaff()) {
            $role = 'staff';
        }
        
        // جلب المحتوى من قاعدة البيانات
        $helpGuides = HelpGuide::forRole($role)
            ->active()
            ->ordered()
            ->get();
        
        // إذا لم يكن هناك محتوى، استخدم المحتوى الافتراضي
        if ($helpGuides->isEmpty()) {
            if ($role === 'admin') {
                return view('help-guide.admin');
            } elseif ($role === 'staff') {
                return view('help-guide.staff');
            } else {
                return view('help-guide.customer');
            }
        }
        
        return view('help-guide.index', compact('helpGuides', 'role'));
    }
}

