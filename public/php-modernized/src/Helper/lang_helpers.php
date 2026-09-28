<?php

/**
 * Global Translation Helper - FtoniNuke v1.4
 */

use FtoniNuke\Service\LanguageManager;

if (!function_exists('__')) {
    /**
     * Translate the given message
     */
    function __(string $key): string
    {
        return LanguageManager::translate($key);
    }
}
