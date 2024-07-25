<?php

namespace App\Listeners;

use Exception;
use App\Events\ExampleEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class LogMasterDataChange implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;

    public function __construct()
    {
        //
    }

    public function handle(ExampleEvent $event): void
    {
        $token = Request::get('auth_token');

        $postData = [
            'username' => $event->user->name,
            'service_id' => 1,
            'service_name' => 'Example Service',
            'ip_address' => request()->ip(),
            'time' => now()->toDateTimeString(),
            'operation' => "{$event->operation} on {$event->entityType} {$event->entityName}",
            'status' => $event->status,
        ];

        $url = env('AUDIT_TRAIL_SERVICE').'/api/logs';

        try {
            $response = Http::withToken($token)->post($url, $postData);

            if ($response->failed()) {
                Log::error('Failed to send log to API', [
                    'response_status' => $response->status(),
                    'response_body' => $response->body(),
                    'postData' => $postData,
                ]);
            }
        } catch (Exception $e) {
            Log::error('Exception while sending log to API', [
                'message' => $e->getMessage(),
                'postData' => $postData,
            ]);
        }
    }
}
