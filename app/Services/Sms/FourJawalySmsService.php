<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SmsLog;

class FourJawalySmsService
{
    /**
     * Send SMS message to a phone number
     *
     * @param string $phone
     * @param string $message
     * @param array $metadata Additional metadata for logging
     * @return array
     */
    public function sendSMS(string $phone, string $message, array $metadata = []): array
    {
        // Check if SMS is enabled
        if (!config('sms.enabled', true) || !config('sms.provider.fourjawaly.enabled', true)) {
            return [
                'success' => false,
                'code' => 400,
                'message' => 'SMS notifications are disabled',
                'provider_response' => null,
            ];
        }

        // Format phone number
        $formattedPhone = $this->formatPhoneNumber($phone);
        
        if (!$formattedPhone) {
            return [
                'success' => false,
                'code' => 400,
                'message' => 'Invalid phone number format',
                'provider_response' => null,
            ];
        }

        // Check rate limiting
        if (!$this->checkRateLimit($formattedPhone)) {
            return [
                'success' => false,
                'code' => 429,
                'message' => 'Rate limit exceeded',
                'provider_response' => null,
            ];
        }

        // Create SMS log entry
        $smsLog = SmsLog::createLog(array_merge([
            'event_type' => $metadata['event_type'] ?? 'manual',
            'entity_type' => $metadata['entity_type'] ?? null,
            'entity_id' => $metadata['entity_id'] ?? null,
            'phone' => $formattedPhone,
            'message' => $message,
        ], $metadata));

        try {
            $response = $this->makeApiRequest($formattedPhone, $message);
            
            if ($response['success']) {
                $smsLog->markAsSent(
                    $response['provider_message_id'] ?? null,
                    $response['provider_response'] ?? null
                );
            } else {
                $smsLog->markAsFailed(
                    $response['message'] ?? 'Unknown error',
                    $response['provider_response'] ?? null
                );
            }

            return $response;
        } catch (\Exception $e) {
            $errorMessage = 'SMS API request failed: ' . $e->getMessage();
            $smsLog->markAsFailed($errorMessage);
            
            Log::error('FourJawaly SMS Error', [
                'phone' => $formattedPhone,
                'message' => $message,
                'error' => $e->getMessage(),
                'metadata' => $metadata,
            ]);

            return [
                'success' => false,
                'code' => 500,
                'message' => $errorMessage,
                'provider_response' => null,
            ];
        }
    }

    /**
     * Make API request to FourJawaly
     */
    private function makeApiRequest(string $phone, string $message): array
    {
        $headers = [
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        ];

        $data = [
            "messages" => [
                [
                    "text" => $message,
                    "numbers" => [$phone],
                    "sender" => config('sms.provider.fourjawaly.sender', 'TechPack')
                ]
            ]
        ];

        $apiKey = config('sms.provider.fourjawaly.api_key');
        $apiSecret = config('sms.provider.fourjawaly.api_secret');
        
        // Check if credentials are missing (for testing)
        if (empty($apiKey) || empty($apiSecret)) {
            Log::warning('FourJawaly SMS credentials missing - skipping actual SMS send (testing mode)');
            return [
                'success' => true,
                'code' => 200,
                'message' => 'SMS send skipped (testing mode - credentials not configured)',
                'provider_message_id' => 'test-' . time(),
                'provider_response' => ['test_mode' => true],
            ];
        }
        
        $response = Http::withHeaders($headers)
            ->timeout(30)
            ->baseUrl(config('sms.provider.fourjawaly.base_url'))
            ->withBasicAuth($apiKey, $apiSecret)
            ->post('account/area/sms/send', $data);

        $responseData = $response->json();
        
        Log::info('FourJawaly SMS Response', [
            'phone' => $phone,
            'status_code' => $response->status(),
            'response' => $responseData
        ]);

        // Parse FourJawaly response
        if ($response->successful() && isset($responseData['code']) && $responseData['code'] == 200) {
            return [
                'success' => true,
                'code' => 200,
                'message' => 'SMS sent successfully',
                'provider_message_id' => $responseData['request_id'] ?? null,
                'provider_response' => $responseData,
            ];
        }

        return [
            'success' => false,
            'code' => $responseData['code'] ?? $response->status(),
            'message' => $responseData['message'] ?? 'SMS sending failed',
            'provider_response' => $responseData,
        ];
    }

    /**
     * Format phone number to international format for Saudi Arabia
     */
    private function formatPhoneNumber(string $phone): ?string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Handle different formats
        if (strlen($phone) == 9 && str_starts_with($phone, '5')) {
            // Format: 5XXXXXXXX (Saudi mobile without country code)
            return '966' . $phone;
        } elseif (strlen($phone) == 10 && str_starts_with($phone, '05')) {
            // Format: 05XXXXXXXX (Saudi mobile with leading zero)
            return '966' . substr($phone, 1);
        } elseif (strlen($phone) == 12 && str_starts_with($phone, '966')) {
            // Format: 9665XXXXXXXX (Saudi mobile with country code)
            return $phone;
        } elseif (strlen($phone) == 13 && str_starts_with($phone, '966')) {
            // Format: 9665XXXXXXXX (Saudi mobile with country code and +)
            return $phone;
        }
        
        // If none of the above formats match, return null
        return null;
    }

    /**
     * Check rate limiting for phone number
     */
    private function checkRateLimit(string $phone): bool
    {
        if (!config('sms.rate_limiting.enabled', true)) {
            return true;
        }

        $now = now();
        $minuteAgo = $now->copy()->subMinute();
        $hourAgo = $now->copy()->subHour();
        $dayAgo = $now->copy()->subDay();

        // Check per minute limit
        $minuteCount = SmsLog::where('phone', $phone)
            ->where('created_at', '>=', $minuteAgo)
            ->count();

        if ($minuteCount >= config('sms.rate_limiting.max_per_minute', 5)) {
            return false;
        }

        // Check per hour limit
        $hourCount = SmsLog::where('phone', $phone)
            ->where('created_at', '>=', $hourAgo)
            ->count();

        if ($hourCount >= config('sms.rate_limiting.max_per_hour', 20)) {
            return false;
        }

        // Check per day limit
        $dayCount = SmsLog::where('phone', $phone)
            ->where('created_at', '>=', $dayAgo)
            ->count();

        if ($dayCount >= config('sms.rate_limiting.max_per_day', 100)) {
            return false;
        }

        return true;
    }

    /**
     * Get SMS delivery status (if supported by provider)
     */
    public function getDeliveryStatus(string $providerMessageId): array
    {
        // FourJawaly doesn't provide delivery status API in current implementation
        // This can be extended if the provider offers webhook or status API
        return [
            'success' => false,
            'message' => 'Delivery status not available',
        ];
    }

    /**
     * Retry failed SMS
     */
    public function retrySms(SmsLog $smsLog): array
    {
        if (!$smsLog->canRetry()) {
            return [
                'success' => false,
                'message' => 'SMS cannot be retried',
            ];
        }

        $smsLog->incrementAttempt();
        
        return $this->sendSMS($smsLog->phone, $smsLog->message, [
            'event_type' => $smsLog->event_type,
            'entity_type' => $smsLog->entity_type,
            'entity_id' => $smsLog->entity_id,
            'retry' => true,
        ]);
    }
}

