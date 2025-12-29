<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Carbon\Carbon;

class UpdateBookingStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update booking statuses automatically based on current time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating booking statuses...');

        // Get all active bookings (not cancelled)
        $bookings = Booking::where('status', '!=', 'cancelled')
            ->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhere('status', 'confirmed')
                    ->orWhere('status', 'in_progress');
            })
            ->get();

        $updatedCount = 0;

        foreach ($bookings as $booking) {
            if ($booking->updateStatusAutomatically()) {
                $updatedCount++;
                $this->line("Updated booking #{$booking->id} to {$booking->status}");
            }
        }

        $this->info("Updated {$updatedCount} booking(s).");
        
        return Command::SUCCESS;
    }
}

