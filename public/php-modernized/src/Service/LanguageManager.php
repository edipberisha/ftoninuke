<?php

declare(strict_types=1);

namespace FtoniNuke\Service;

/**
 * LanguageManager - Handles localized string retrieval from the lang/ directory.
 */
class LanguageManager
{
    private static ?array $dictionary = null;
    private static string $langDir;

    public static function init(string $langDir): void
    {
        self::$langDir = $langDir;
        $locale = self::getLocale();
        self::loadDictionary($locale);
    }

    /**
     * Get the current locale from config or session (for installer)
     */
    public static function getLocale(): string
    {
        // 1. Check Session (Installer override)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['setup_locale'])) {
            return $_SESSION['setup_locale'];
        }

        // 2. Check Config File
        $configPath = self::$langDir . '/../config/app.php';
        if (file_exists($configPath)) {
            $config = include $configPath;
            return $config['locale'] ?? 'en';
        }

        return 'en';
    }

    /**
     * Load the translation file for the given locale
     */
    private static function loadDictionary(string $locale): void
    {
        $file = self::$langDir . '/' . strtolower($locale) . '.php';
        
        if (file_exists($file)) {
            self::$dictionary = include $file;
        } else {
            // Fallback to English
            $fallback = self::$langDir . '/en.php';
            self::$dictionary = file_exists($fallback) ? include $fallback : [];
        }
    }

    /**
     * Translate a key
     */
    public static function translate(string $key): string
    {
        if (self::$dictionary === null) {
            self::init(__DIR__ . '/../../lang');
        }

        return self::$dictionary[$key] ?? $key;
    }
}
