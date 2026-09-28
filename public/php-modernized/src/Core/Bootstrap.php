<?php

declare(strict_types=1);

namespace FtoniNuke\Core;

use FtoniNuke\Core\Database;
use RuntimeException;

/**
 * Modern Bootstrap Class
 * 
 * Replaces legacy mainfile.php and handles system initialization,
 * including autoloading, configuration, and security.
 */
class Bootstrap
{
    private static ?self $instance = null;
    private Database $db;
    private array $config = [];

    private function __construct()
    {
        $this->init();
    }

    public static function run(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function init(): void
    {
        // 1. Initialize Error Handling
        error_reporting(E_ALL);
        ini_set('display_errors', '1'); // Enabled for development

        // 2. Load Language Support
        require_once __DIR__ . '/../Helper/lang_helpers.php';
        \FtoniNuke\Service\LanguageManager::init(__DIR__ . '/../../lang');

        // 3. Load Configuration (Environment variables via Dotenv recommended)
        $this->loadDotEnv();

        // 4. Initialize Database
        $this->db = Database::getInstance();

        // 4. Session Security
        $this->startSecureSession();

        // 5. Global variable bridge (For legacy module compatibility)
        $this->setupLegacyBridge();
    }

    private function loadDotEnv(): void
    {
        // Placeholder for Dotenv loading logic
        // composer require vlucas/phpdotenv
    }

    private function startSecureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure'   => isset($_SERVER['HTTPS']),
                'cookie_samesite' => 'Lax',
                'use_strict_mode' => true,
            ]);
        }
    }

    /**
     * Bridge legacy globals to modern Core
     */
    private function setupLegacyBridge(): void
    {
        global $db, $prefix;
        
        $db = $this->db;
        $prefix = $_ENV['DB_PREFIX'] ?? 'nuke_';
        
        // This allows old modules.php logic to use the new PDO wrapper
        // through the global $db variable.
    }

    /**
     * Generate or retrieve a CSRF token for the session
     */
    public function getCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            $this->startSecureSession();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Validate a provided CSRF token
     */
    public function validateCsrfToken(?string $token): bool
    {
        return !empty($token) && hash_equals($this->getCsrfToken(), $token);
    }

    public function getDb(): Database
    {
        return $this->db;
    }

    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }
}
