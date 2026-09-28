<?php

declare(strict_types=1);

namespace FtoniNuke\Security;

/**
 * JwtManager - Secure, native JSON Web Token implementation for Headless CMS features.
 */
class JwtManager
{
    private string $secret;
    private string $algo = 'SHA256';

    public function __construct()
    {
        $this->secret = $_ENV['JWT_SECRET'] ?? 'default_secret_key_change_me';
    }

    /**
     * Create a token
     */
    public function generateToken(array $payload, int $ttl = 3600): string
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        $payload['iat'] = time();
        $payload['exp'] = time() + $ttl;
        $payload = json_encode($payload);

        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);

        $signature = hash_hmac($this->algo, $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Validate and decode token
     */
    public function validateToken(string $token): ?array
    {
        $tokenParts = explode('.', $token);
        if (count($tokenParts) !== 3) return null;

        $header = $this->base64UrlDecode($tokenParts[0]);
        $payload = $this->base64UrlDecode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];

        // Check expiration
        $decodedPayload = json_decode($payload, true);
        if (!$decodedPayload || (isset($decodedPayload['exp']) && $decodedPayload['exp'] < time())) {
            return null;
        }

        // Verify signature
        $base64UrlHeader = $tokenParts[0];
        $base64UrlPayload = $tokenParts[1];
        $signature = hash_hmac($this->algo, $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        if (!hash_equals($base64UrlSignature, $signatureProvided)) {
            return null;
        }

        return $decodedPayload;
    }

    private function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    private function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return (string)base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }
}
