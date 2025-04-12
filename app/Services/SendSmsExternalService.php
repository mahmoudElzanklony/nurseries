<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SendSmsExternalService
{
    public function send($phone, $text)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('SMS_BEARER_TOKEN'), // Add Bearer token
            'Accept' => 'application/json', // Typically good practice for APIs
        ])->post('https://api.oursms.com/msgs/sms', [
            'src'=>'Mraken',
            'username'=>'Mraken-AD',
            'token' => env('SMS_TOKEN'),
            'dests' => [$phone],
            'body' => $text,
        ]);
        return $response->json();
        if ($response->successful()) {
            // SMS sent successfully
            return $response->json();
        } else {
            // Handle error
            return response()->json(['error' => 'Failed to send SMS'], 500);
        }
    }
}
