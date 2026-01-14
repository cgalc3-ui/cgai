<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerFacingController extends Controller
{
    /**
     * Display the main customer facing page
     */
    public function index()
    {
        return view('admin.customer-facing.index');
    }

    /**
     * Load a specific section content via AJAX (content only, no layout)
     */
    public function loadSection(Request $request, $section)
    {
        $allowedSections = [
            'navigation',
            'hero',
            'company-logo',
            'footer',
            'consultation-booking-section',
            'technologies-section',
            'services-section',
            'ready-apps-section',
        ];

        if (!in_array($section, $allowedSections)) {
            return response()->json([
                'success' => false,
                'message' => 'Section not found'
            ], 404);
        }

        // Map section names to controller methods
        $controllerMap = [
            'navigation' => [\App\Http\Controllers\Admin\NavigationController::class, 'index'],
            'hero' => [\App\Http\Controllers\Admin\HeroController::class, 'index'],
            'company-logo' => [\App\Http\Controllers\Admin\CompanyLogoController::class, 'index'],
            'footer' => [\App\Http\Controllers\Admin\FooterController::class, 'index'],
            'consultation-booking-section' => [\App\Http\Controllers\Admin\ConsultationBookingSectionController::class, 'index'],
            'technologies-section' => [\App\Http\Controllers\Admin\TechnologiesSectionController::class, 'index'],
            'services-section' => [\App\Http\Controllers\Admin\HomeServicesSectionController::class, 'index'],
            'ready-apps-section' => [\App\Http\Controllers\Admin\HomeReadyAppsSectionController::class, 'index'],
        ];

        if (!isset($controllerMap[$section])) {
            return response()->json([
                'success' => false,
                'message' => 'Section controller not found'
            ], 404);
        }

        [$controllerClass, $method] = $controllerMap[$section];
        $controller = app($controllerClass);
        
        // Call the controller method
        $response = $controller->$method($request);
        
        // If it's a view response, render only the content section
        if ($response instanceof \Illuminate\View\View || $response instanceof \Illuminate\Contracts\View\View) {
            $view = $response;
            
            // Render the view with full layout first
            $fullHtml = view($view->getName(), $view->getData())->render();
            
            // Extract content from main.main-content using regex
            // Look for <main class="main-content">...</main> (handle multiline and nested tags)
            $pattern = '/<main\s+class\s*=\s*["\']main-content["\'][^>]*>(.*?)<\/main>/is';
            if (preg_match($pattern, $fullHtml, $matches)) {
                $content = $matches[1];
                
                // Remove top-bar (header) from content
                $content = preg_replace('/<header\s+class\s*=\s*["\']top-bar["\'][^>]*>.*?<\/header>/is', '', $content);
                
                // Remove any remaining sidebar or navigation elements
                $content = preg_replace('/<aside[^>]*>.*?<\/aside>/is', '', $content);
                $content = preg_replace('/<nav[^>]*class\s*=\s*["\'][^"\']*sidebar[^"\']*["\'][^>]*>.*?<\/nav>/is', '', $content);
                
                // Clean up any extra whitespace
                $content = trim($content);
                
                return response($content)->header('Content-Type', 'text/html; charset=utf-8');
            }
            
            // Fallback: try to find any main tag
            if (preg_match('/<main[^>]*>(.*?)<\/main>/is', $fullHtml, $matches)) {
                $content = $matches[1];
                
                // Remove top-bar and sidebar
                $content = preg_replace('/<header\s+class\s*=\s*["\']top-bar["\'][^>]*>.*?<\/header>/is', '', $content);
                $content = preg_replace('/<aside[^>]*>.*?<\/aside>/is', '', $content);
                
                return response(trim($content))->header('Content-Type', 'text/html; charset=utf-8');
            }
            
            // Last fallback: try to extract from body or return as is
            // Remove known layout elements using string replacement
            $content = $fullHtml;
            $content = preg_replace('/<aside[^>]*>.*?<\/aside>/is', '', $content);
            $content = preg_replace('/<header[^>]*class\s*=\s*["\'][^"\']*top-bar[^"\']*["\'][^>]*>.*?<\/header>/is', '', $content);
            $content = preg_replace('/<nav[^>]*class\s*=\s*["\'][^"\']*sidebar[^"\']*["\'][^>]*>.*?<\/nav>/is', '', $content);
            
            return response($content)->header('Content-Type', 'text/html; charset=utf-8');
        }
        
        // If it's a redirect response, return the redirect URL
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return response()->json([
                'redirect' => $response->getTargetUrl()
            ]);
        }
        
        // If it's a redirect or JSON, return as is
        return $response;
    }
}

