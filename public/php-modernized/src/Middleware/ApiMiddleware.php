<?php

declare(strict_types=1);

namespace FtoniNuke\Middleware;

use RuntimeException;

/**
 * ApiMiddleware - Handles API Key authentication and Rate Limiting.
 */
class ApiMiddleware
{
    private string $apiKey;
    private string $rateLimitDir;

    public function __construct()
    {
        $this->apiKey = $_ENV['API_KEY'] ?? 'ftoni-master-dev-key';
        $this->rateLimitDir = __DIR__ . '/../../cache/api_limits';

        if (!is_dir($this->rateLimitDir)) {
            mkdir($this->rateLimitDir, 0755, true);
        }
    }

    /**
     * Handle incoming request security
     */
    public function handle(): void
    {
        // 1. API Key Check
        $providedKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
        if ($providedKey !== $this->apiKey) {
            $this->respond401("Invalid or missing API Key.");
        }

        // 2. Rate Limiting (100 requests / 1 minute per IP)
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $cacheFile = $this->rateLimitDir . '/' . md5($ip) . '.json';
        
        $data = file_exists($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : ['count' => 0, 'reset' => time() + 60];

        if (time() > $data['reset']) {
            $data = ['count' => 1, 'reset' => time() + 60];
        } else {
            $data['count']++;
        }

        file_put_contents($cacheFile, json_encode($data));

        if ($data['count'] > 100) {
            $this->respond429("Rate limit exceeded. Please wait.");
        }
    }

    private function respond401(string $msg): void
    {
        header('Content-Type: application/json', true, 401);
        echo json_encode(['error' => 'Unauthorized', 'message' => $msg]);
        exit;
    }

    private function respond429(string $msg): void
    {
        header('Content-Type: application/json', true, 429);
        echo json_encode(['error' => 'Too Many Requests', 'message' => $msg]);
        exit;
    }
}
