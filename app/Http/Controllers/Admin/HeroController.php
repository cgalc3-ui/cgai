<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroController extends Controller
{
    public function index()
    {
        $hero = HeroSection::first();

        return view('admin.hero.index', compact('hero'));
    }

    public function create()
    {
        $view = view('admin.hero.hero-modal');
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'heading' => 'required|string|max:255',
            'heading_en' => 'nullable|string|max:255',
            'subheading' => 'nullable|string|max:255',
            'subheading_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'background_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg,bmp',
            'buttons' => 'nullable|array',
            'buttons.*.title' => 'required_with:buttons|string|max:255',
            'buttons.*.title_en' => 'nullable|string|max:255',
            'buttons.*.link' => 'required_with:buttons|string|max:255',
            'buttons.*.target' => 'nullable|string|in:_self,_blank',
            'buttons.*.style' => 'nullable|string|in:primary,secondary',
        ]);

        // Delete old hero if exists
        $oldHero = HeroSection::first();
        if ($oldHero) {
            if ($oldHero->background_image) {
                Storage::disk('public')->delete($oldHero->background_image);
            }
            $oldHero->delete();
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('background_image')) {
            $data['background_image'] = $request->file('background_image')->store('hero', 'public');
        }

        HeroSection::create($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.hero_updated'),
            'redirect' => route('admin.customer-facing.hero.index')
        ]);
    }

    public function edit(HeroSection $hero)
    {
        $view = view('admin.hero.hero-modal', compact('hero'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(Request $request, HeroSection $hero)
    {
        $request->validate([
            'heading' => 'required|string|max:255',
            'heading_en' => 'nullable|string|max:255',
            'subheading' => 'nullable|string|max:255',
            'subheading_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'background_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg,bmp',
            'buttons' => 'nullable|array',
            'buttons.*.title' => 'required_with:buttons|string|max:255',
            'buttons.*.title_en' => 'nullable|string|max:255',
            'buttons.*.link' => 'required_with:buttons|string|max:255',
            'buttons.*.target' => 'nullable|string|in:_self,_blank',
            'buttons.*.style' => 'nullable|string|in:primary,secondary',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('background_image')) {
            // Delete old image
            if ($hero->background_image) {
                Storage::disk('public')->delete($hero->background_image);
            }
            $data['background_image'] = $request->file('background_image')->store('hero', 'public');
        } else {
            // Keep existing image
            unset($data['background_image']);
        }

        $hero->update($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.hero_updated'),
            'redirect' => route('admin.customer-facing.hero.index')
        ]);
    }

    public function destroy(HeroSection $hero)
    {
        if ($hero->background_image) {
            Storage::disk('public')->delete($hero->background_image);
        }

        $hero->delete();

        return redirect()->route('admin.customer-facing.hero.index')
            ->with('success', __('messages.hero_deleted'));
    }
}

