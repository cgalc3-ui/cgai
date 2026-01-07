<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetApiLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Priority 1: Query parameter (e.g., ?locale=en)
        $locale = $request->query('locale');
        
        // Priority 2: Header 'locale' (custom header)
        if (!$locale) {
            $locale = $request->header('locale');
        }
        
        // Priority 3: Header X-Locale
        if (!$locale) {
            $locale = $request->header('X-Locale');
        }
        
        // Priority 4: Header Accept-Language
        if (!$locale) {
            $acceptLanguage = $request->header('Accept-Language');
            if ($acceptLanguage) {
                // Handle formats like "en-US,en;q=0.9" or "ar-SA,ar;q=0.9"
                $locale = strtolower(substr(trim(explode(',', $acceptLanguage)[0]), 0, 2));
            }
        }
        
        // Priority 5: Check URL path (e.g., /api/en/... or /api/ar/...)
        if (!$locale) {
            $path = $request->path();
            $pathSegments = explode('/', $path);
            
            // Check if first segment after 'api' is a locale
            if (isset($pathSegments[0]) && $pathSegments[0] === 'api' && isset($pathSegments[1])) {
                $potentialLocale = strtolower($pathSegments[1]);
                if (in_array($potentialLocale, ['ar', 'en'])) {
                    $locale = $potentialLocale;
                }
            }
        }
        
        // Normalize locale (handle 'en-US' -> 'en', 'ar-SA' -> 'ar')
        if ($locale && strpos($locale, '-') !== false) {
            $locale = substr($locale, 0, 2);
        }
        
        // Normalize locale value
        if ($locale) {
            $locale = strtolower(trim($locale));
        }
        
        // Validate and set locale
        if ($locale && in_array($locale, ['ar', 'en'])) {
            App::setLocale($locale);
        } else {
            // Default to config locale
            App::setLocale(config('app.locale'));
        }

        return $next($request);
    }
}

