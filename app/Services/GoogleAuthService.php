<?php

namespace App\Services;

use App\Models\Setting;

class GoogleAuthService
{
    public static function getClientId(): ?string
    {
        $id = Setting::get('google_client_id');
        return (!empty($id) && !str_contains($id, 'YOUR_')) ? trim($id) : null;
    }

    public static function getClientSecret(): ?string
    {
        $secret = Setting::get('google_client_secret');
        return (!empty($secret) && !str_contains($secret, 'YOUR_')) ? trim($secret) : null;
    }

    public static function isConfigured(): bool
    {
        return !empty(self::getClientId()) && !empty(self::getClientSecret());
    }

    public static function getRedirectUri(): string
    {
        $path = url('/auth/google/callback');
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        return $protocol . '://' . $host . $path;
    }

    public static function getAuthUrl(): ?string
    {
        $clientId = self::getClientId();
        if (!$clientId) {
            return null;
        }

        $redirectUri = urlencode(self::getRedirectUri());
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;

        return "https://accounts.google.com/o/oauth2/v2/auth?" .
               "response_type=code&" .
               "client_id={$clientId}&" .
               "redirect_uri={$redirectUri}&" .
               "scope=openid%20email%20profile&" .
               "state={$state}&" .
               "prompt=select_account";
    }

    public static function getUserInfoFromCode(string $code): ?array
    {
        $clientId = self::getClientId();
        $clientSecret = self::getClientSecret();
        $redirectUri = self::getRedirectUri();

        if (!$clientId || !$clientSecret) {
            return null;
        }

        // 1. Exchange auth code for Access Token
        $tokenUrl = "https://oauth2.googleapis.com/token";
        $postData = http_build_query([
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $tokenData = json_decode($response, true);
        if (empty($tokenData['access_token'])) {
            return null;
        }

        $accessToken = $tokenData['access_token'];

        // 2. Fetch User Profile Info using Access Token
        $userInfoUrl = "https://www.googleapis.com/oauth2/v2/userinfo";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$accessToken}"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $userResponse = curl_exec($ch);
        curl_close($ch);

        return json_decode($userResponse, true);
    }
}
