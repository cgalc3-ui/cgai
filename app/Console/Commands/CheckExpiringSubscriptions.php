<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckExpiringSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expiring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for subscriptions expiring within 7 days and send notifications';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiringDate = Carbon::now()->addDays(7);

        $expiringSubscriptions = UserSubscription::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $expiringDate)
            ->where('expires_at', '>', Carbon::now())
            ->with(['user', 'subscription'])
            ->get();

        $count = 0;

        foreach ($expiringSubscriptions as $subscription) {
            // Check if notification was sent in the last 24 hours
            $recentNotification = $subscription->user->notifications()
                ->where('type', 'subscription_expiring')
                ->where('data->subscription_id', $subscription->id)
                ->where('created_at', '>', Carbon::now()->subDay())
                ->exists();

            if (!$recentNotification) {
                $this->notificationService->send(
                    $subscription->user,
                    'subscription_expiring',
                    'messages.subscription_expiring_soon',
                    'messages.subscription_expiring_for_package',
                    [
                        'subscription_id' => $subscription->id,
                        'expires_at' => $subscription->expires_at->toDateTimeString(),
                        'package' => $subscription->subscription->name,
                        'expires_in' => $subscription->expires_at->diffForHumans(),
                    ]
                );

                $count++;
            }
        }

        $this->info("تم إرسال {$count} إشعار للاشتراكات القريبة من الانتهاء");

        return Command::SUCCESS;
    }
}
