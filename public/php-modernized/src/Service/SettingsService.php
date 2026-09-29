<?php

declare(strict_types=1);

namespace FtoniNuke\Service;

use FtoniNuke\Core\Database;

class SettingsService
{
    private static ?array $cache = null;
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Tüm ayarları getir (cache'li)
     */
    public function all(): array
    {
        if (self::$cache === null) {
            try {
                self::$cache = $this->db->fetch("SELECT * FROM nuke_config LIMIT 1") ?: [];
            } catch (\Throwable $e) {
                self::$cache = [];
            }
        }
        return self::$cache;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();
        return $all[$key] ?? $default;
    }

    public function bool(string $key, bool $default = false): bool
    {
        $v = $this->get($key);
        if ($v === null) return $default;
        return (int)$v === 1;
    }

    public function int(string $key, int $default = 0): int
    {
        $v = $this->get($key);
        return $v === null ? $default : (int)$v;
    }

    /**
     * Ayarları güncelle
     */
    public function update(array $data): void
    {
        $allowed = [
            'sitename', 'theme',
            'registration_enabled', 'email_verification', 'default_user_level',
            'comments_require_approval', 'captcha_enabled', 'comment_rate_limit',
        ];

        $sets = [];
        $values = [];

        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                $sets[] = "$key = ?";
                $values[] = $data[$key];
            }
        }

        if (empty($sets)) return;

        // Kayıt var mı? Yoksa oluştur
        $exists = $this->db->fetch("SELECT id FROM nuke_config LIMIT 1");
        if (!$exists) {
            $this->db->query(
                "INSERT INTO nuke_config (sitename, theme, api_key) VALUES (?, ?, ?)",
                ['FtoniNuke', 'default', bin2hex(random_bytes(16))]
            );
        }

        $this->db->query("UPDATE nuke_config SET " . implode(', ', $sets) . " WHERE id = 1", $values);

        // Cache temizle
        self::$cache = null;
    }

    // Kısayol metodları
    public function isRegistrationEnabled(): bool
    {
        return $this->bool('registration_enabled', false);
    }

    public function isCaptchaEnabled(): bool
    {
        return $this->bool('captcha_enabled', true);
    }

    public function commentsRequireApproval(): bool
    {
        return $this->bool('comments_require_approval', true);
    }

    public function commentRateLimit(): int
    {
        return $this->int('comment_rate_limit', 5);
    }
}
