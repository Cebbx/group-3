<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SmsLog;

class SmsService
{
    /**
     * Send SMS to driver via Semaphore API or fallback to simulation log.
     */
    public static function send($driver, string $message): void
    {
        $phone = $driver->contact_number;
        $driverId = $driver->id;
        
        $apiKey = config('services.semaphore.key');
        $senderName = config('services.semaphore.sender');

        // Always save to database logs for the in-app simulator
        SmsLog::create([
            'driver_id' => $driverId,
            'phone_number' => $phone ?? 'N/A',
            'message' => $message,
        ]);

        // If API key is set, attempt to send real SMS
        if (!empty($apiKey) && !empty($phone)) {
            try {
                $response = Http::post('https://api.semaphore.co/api/v4/messages', [
                    'apikey' => $apiKey,
                    'number' => $phone,
                    'message' => $message,
                    'sendername' => $senderName,
                ]);

                if ($response->successful()) {
                    Log::info("Semaphore SMS sent successfully to {$driver->name} ({$phone}).");
                } else {
                    Log::error("Semaphore SMS failed to send to {$driver->name}. Response: " . $response->body());
                }
            } catch (\Exception $e) {
                Log::error("Error sending SMS via Semaphore: " . $e->getMessage());
            }
        } else {
            // Log for local development / simulation
            Log::info("SMS simulated (no Semaphore API key configured) for {$driver->name} ({$phone}):\n{$message}");
        }
    }
}
