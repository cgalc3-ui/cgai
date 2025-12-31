<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use Illuminate\Console\Command;

class UpdateExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update expired subscriptions status to expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updated = UserSubscription::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        $this->info("تم تحديث {$updated} اشتراك منتهي");

        return Command::SUCCESS;
    }
}
