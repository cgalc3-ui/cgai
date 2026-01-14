<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NavigationController extends Controller
{
    public function index()
    {
        $logo = NavigationItem::byType('logo')->first();
        $menuItems = NavigationItem::byType('menu_item')->active()->get();
        $buttons = NavigationItem::byType('button')->active()->get();

        return view('admin.navigation.index', compact('logo', 'menuItems', 'buttons'));
    }

    // Logo Management
    public function createLogo()
    {
        $view = view('admin.navigation.logo-modal');
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function storeLogo(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg,bmp',
        ]);

        // Delete old logo if exists
        $oldLogo = NavigationItem::byType('logo')->first();
        if ($oldLogo) {
            if ($oldLogo->image) {
                Storage::disk('public')->delete($oldLogo->image);
            }
            $oldLogo->delete();
        }

        $data = $request->all();
        $data['item_type'] = 'logo';
        $data['is_active'] = true;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('navigation', 'public');
        }

        NavigationItem::create($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.logo_updated'),
            'redirect' => route('admin.customer-facing.navigation.index')
        ]);
    }

    // Menu Items Management
    public function createMenuItem()
    {
        $view = view('admin.navigation.menu-item-modal');
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function storeMenuItem(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'target' => 'nullable|string|in:_self,_blank',
        ]);

        $data = $request->all();
        $data['item_type'] = 'menu_item';
        $data['is_active'] = $request->has('is_active');

        NavigationItem::create($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.menu_item_created'),
            'redirect' => route('admin.customer-facing.navigation.index')
        ]);
    }

    public function editMenuItem(NavigationItem $navigationItem)
    {
        $view = view('admin.navigation.menu-item-modal', compact('navigationItem'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function updateMenuItem(Request $request, NavigationItem $navigationItem)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'target' => 'nullable|string|in:_self,_blank',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $navigationItem->update($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.menu_item_updated'),
            'redirect' => route('admin.customer-facing.navigation.index')
        ]);
    }

    // Buttons Management
    public function createButton()
    {
        $view = view('admin.navigation.button-modal');
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function storeButton(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'link' => 'required|string|max:255',
            'target' => 'nullable|string|in:_self,_blank',
        ]);

        $data = $request->all();
        $data['item_type'] = 'button';
        $data['is_active'] = $request->has('is_active');

        NavigationItem::create($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.button_created'),
            'redirect' => route('admin.customer-facing.navigation.index')
        ]);
    }

    public function editButton(NavigationItem $navigationItem)
    {
        $view = view('admin.navigation.button-modal', compact('navigationItem'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function updateButton(Request $request, NavigationItem $navigationItem)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'link' => 'required|string|max:255',
            'target' => 'nullable|string|in:_self,_blank',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $navigationItem->update($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.button_updated'),
            'redirect' => route('admin.customer-facing.navigation.index')
        ]);
    }

    public function destroy(NavigationItem $navigationItem)
    {
        if ($navigationItem->image) {
            Storage::disk('public')->delete($navigationItem->image);
        }

        $navigationItem->delete();

        return redirect()->route('admin.customer-facing.navigation.index')
            ->with('success', __('messages.item_deleted'));
    }
}
