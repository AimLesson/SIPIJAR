<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class NotificationHelper
{
    public static function sendWhatsApp($target, $message)
    {
        $token = env('FONNTE_TOKEN');
    
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ],
            CURLOPT_HTTPHEADER => [
                'Authorization: WpnbU2JAAetCeWb7XU3P' //change TOKEN to your actual token
            ],
        ]);
    
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
    
        // Logging to Laravel log
        if ($httpCode === 200) {
            Log::info('[WA SUCCESS]', [
                'target' => $target,
                'message' => $message,
                'response' => $response,
            ]);
        } else {
            Log::error('[WA FAILED]', [
                'target' => $target,
                'message' => $message,
                'response' => $response,
                'status' => $httpCode,
            ]);
        }
    
        return $response;
    }
    
}
