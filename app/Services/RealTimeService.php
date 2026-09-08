<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RealTimeService
{
    public function now(): ?Carbon
    {
        try {
            $response = Http::connectTimeout(5)
                ->timeout(10)
                ->retry(2, 300)
                ->get('https://utctime.app/api/now/Asia/Kathmandu');

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data['datetime'])) {
                    return Carbon::parse($data['datetime'])
                        ->setTimezone('Asia/Kathmandu');
                }

                if (!empty($data['local_iso'])) {
                    return Carbon::parse($data['local_iso'])
                        ->setTimezone('Asia/Kathmandu');
                }
            }

            Log::error('External time API failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('External time API error', [
                'message' => $e->getMessage(),
            ]);
        }

        return null;
    }
}