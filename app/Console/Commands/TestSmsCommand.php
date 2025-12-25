<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Sms\FourJawalySmsService;

class TestSmsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:test {phone} {message?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test SMS sending to a phone number';

    /**
     * Execute the console command.
     */
    public function handle(FourJawalySmsService $smsService)
    {
        $phone = $this->argument('phone');
        $message = $this->argument('message') ?? 'رسالة اختبار من النظام';

        $this->info("Sending SMS to: {$phone}");
        $this->info("Message: {$message}");

        $result = $smsService->sendSMS($phone, $message, [
            'event_type' => 'test',
        ]);

        if ($result['success']) {
            $this->info('✅ SMS sent successfully!');
            $this->info('Response: ' . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->error('❌ SMS sending failed!');
            $this->error('Error: ' . $result['message']);
            $this->error('Response: ' . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        return $result['success'] ? 0 : 1;
    }
}
