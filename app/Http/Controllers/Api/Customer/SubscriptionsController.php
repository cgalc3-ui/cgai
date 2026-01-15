<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionsSection;
use Illuminate\Http\Request;
class SubscriptionsController extends Controller
{

    /**
     * Get all active subscriptions with section header for frontend
     */
    public function index(Request $request)
    {
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        // Get section header
        $section = SubscriptionsSection::where('is_active', true)->first();

        // Get active subscriptions
        $subscriptions = Subscription::where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();

        // Format subscriptions data
        $formattedSubscriptions = $subscriptions->map(function ($subscription) use ($locale) {
            return [
                'id' => $subscription->id,
                'name' => $locale === 'en' && $subscription->name_en 
                    ? $subscription->name_en 
                    : $subscription->name,
                'description' => $locale === 'en' && $subscription->description_en 
                    ? $subscription->description_en 
                    : ($subscription->description ?? ''),
                'price' => number_format($subscription->price, 2),
                'price_raw' => (float) $subscription->price,
                'duration_type' => $subscription->duration_type,
                'duration_text' => $this->getDurationText($subscription->duration_type, $locale),
                'features' => $this->formatFeatures($subscription, $locale),
                'is_pro' => (bool) ($subscription->is_pro ?? false),
                'ai_enabled' => (bool) $subscription->ai_enabled,
            ];
        });

        // Format section data
        $sectionData = null;
        if ($section) {
            $sectionData = [
                'title' => $locale === 'en' && $section->title_en 
                    ? $section->title_en 
                    : ($section->title ?? ''),
                'description' => $locale === 'en' && $section->description_en 
                    ? $section->description_en 
                    : ($section->description ?? ''),
                'background_color' => $section->background_color ?? '#02c0ce',
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'section' => $sectionData,
                'subscriptions' => $formattedSubscriptions,
            ],
        ]);
    }

    /**
     * Get single subscription details
     */
    public function show(Request $request, $id)
    {
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $subscription = Subscription::where('is_active', true)->findOrFail($id);

        $formattedSubscription = [
            'id' => $subscription->id,
            'name' => $locale === 'en' && $subscription->name_en 
                ? $subscription->name_en 
                : $subscription->name,
            'description' => $locale === 'en' && $subscription->description_en 
                ? $subscription->description_en 
                : ($subscription->description ?? ''),
            'price' => number_format($subscription->price, 2),
            'price_raw' => (float) $subscription->price,
            'duration_type' => $subscription->duration_type,
            'duration_text' => $this->getDurationText($subscription->duration_type, $locale),
            'features' => $this->formatFeatures($subscription, $locale),
            'is_pro' => (bool) ($subscription->is_pro ?? false),
            'ai_enabled' => (bool) $subscription->ai_enabled,
            'max_debtors' => $subscription->max_debtors ?? 0,
            'max_messages' => $subscription->max_messages ?? 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $formattedSubscription,
        ]);
    }

    /**
     * Format subscription features based on locale
     */
    private function formatFeatures($subscription, $locale)
    {
        $features = [];
        $sourceFeatures = ($locale === 'en' && !empty($subscription->features_en))
                            ? $subscription->features_en
                            : ($subscription->features ?? []);

        // Fallback to the other language if the preferred one is empty
        if (empty($sourceFeatures) && $locale === 'en' && !empty($subscription->features)) {
            $sourceFeatures = $subscription->features;
        } elseif (empty($sourceFeatures) && $locale === 'ar' && !empty($subscription->features_en)) {
            $sourceFeatures = $subscription->features_en;
        }

        foreach ($sourceFeatures as $feature) {
            if (is_string($feature)) {
                $features[] = ['name' => $feature];
            } elseif (is_array($feature) && isset($feature['name'])) {
                $features[] = ['name' => $feature['name']];
            } elseif (is_array($feature) && isset($feature['name_en'])) {
                $features[] = ['name' => $feature['name_en']];
            }
        }
        
        return array_filter($features, fn($f) => !empty($f['name']));
    }

    /**
     * Get duration text based on locale
     */
    private function getDurationText($durationType, $locale)
    {
        $texts = [
            'ar' => [
                'month' => 'شهري',
                'year' => 'سنوي',
                'lifetime' => 'دائم',
            ],
            'en' => [
                'month' => 'Monthly',
                'year' => 'Yearly',
                'lifetime' => 'Lifetime',
            ],
        ];

        return $texts[$locale][$durationType] ?? $durationType;
    }
}

