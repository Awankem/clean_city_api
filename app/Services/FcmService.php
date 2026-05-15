<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FcmService
{
    /**
     * Send a push notification via FCM V1 API.
     */
    public static function send($token, $title, $body, $data = [])
    {
        $projectId = config('services.fcm.project_id');
        $accessToken = self::getAccessToken();

        if (!$accessToken) {
            Log::error("FCM: Failed to obtain access token.");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => array_map('strval', $data), // V1 requires data values to be strings
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                        ]
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                Log::error("FCM V1 Error: " . $response->body());
            }

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("FCM Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get OAuth2 Access Token using the Service Account JSON (Pure PHP Implementation).
     */
    private static function getAccessToken()
    {
        return Cache::remember('fcm_access_token', 3500, function () {
            $path = config('services.fcm.service_account');
            
            if (!file_exists($path)) {
                Log::error("FCM: Service account file not found at " . $path);
                return null;
            }

            $config = json_decode(file_get_contents($path), true);
            $now = time();
            
            // 1. JWT Header
            $header = base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));

            // 2. JWT Claim Set
            $claimSet = base64UrlEncode(json_encode([
                'iss' => $config['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ]));

            // 3. Signature
            $signatureInput = $header . "." . $claimSet;
            $privateKey = $config['private_key'];
            
            $signature = '';
            if (!openssl_sign($signatureInput, $signature, $privateKey, 'SHA256')) {
                Log::error("FCM: Failed to sign JWT.");
                return null;
            }
            $signature = base64UrlEncode($signature);

            // 4. Construct JWT
            $jwt = $header . "." . $claimSet . "." . $signature;

            // 5. Exchange JWT for Access Token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->failed()) {
                Log::error("FCM Token Exchange Failed: " . $response->body());
                return null;
            }

            return $response->json('access_token');
        });
    }
}

/**
 * Helper function for Base64Url Encoding
 */
function base64UrlEncode($data) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}
