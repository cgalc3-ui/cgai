<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Add locale middleware for API routes
        $middleware->api(prepend: [
            \App\Http\Middleware\SetApiLocale::class,
        ]);

        // Register role middleware alias
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
        ]);
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule): void {
        // Update booking statuses every minute
        $schedule->command('bookings:update-statuses')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();

        // Update expired subscriptions daily
        $schedule->command('subscriptions:update-expired')
            ->daily()
            ->withoutOverlapping();

        // Check expiring subscriptions daily
        $schedule->command('subscriptions:check-expiring')
            ->daily()
            ->withoutOverlapping();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle ModelNotFoundException for API routes
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') || $request->expectsJson() || $request->wantsJson()) {
                try {
                    $modelName = class_basename($e->getModel());
                } catch (\Exception $ex) {
                    $modelName = 'Model';
                }
                
                $message = match($modelName) {
                    'Booking' => 'الحجز المطلوب غير موجود',
                    'Service' => 'الخدمة المطلوبة غير موجودة',
                    'Consultation' => 'الاستشارة المطلوبة غير موجودة',
                    default => 'السجل المطلوب غير موجود',
                };
                
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 404);
            }
        });

        // Handle NotFoundHttpException for API routes (when ModelNotFoundException is converted)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') || $request->expectsJson() || $request->wantsJson()) {
                // Check if the original exception was a ModelNotFoundException
                $previous = $e->getPrevious();
                if ($previous instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    try {
                        $modelName = class_basename($previous->getModel());
                    } catch (\Exception $ex) {
                        $modelName = 'Model';
                    }
                    
                    $message = match($modelName) {
                        'Booking' => 'الحجز المطلوب غير موجود',
                        'Service' => 'الخدمة المطلوبة غير موجودة',
                        'Consultation' => 'الاستشارة المطلوبة غير موجودة',
                        default => 'السجل المطلوب غير موجود',
                    };
                    
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 404);
                }
                
                // Also handle general 404 for API routes
                return response()->json([
                    'success' => false,
                    'message' => 'المورد المطلوب غير موجود',
                ], 404);
            }
        });
    })->create();
