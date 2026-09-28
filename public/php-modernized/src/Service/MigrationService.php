<?php

declare(strict_types=1);

namespace FtoniNuke\Service;

use FtoniNuke\Core\Database;
use PDOException;

/**
 * MigrationService - Automatically builds the FtoniNuke v1.3 database ecosystem.
 */
class MigrationService
{
    private ?Database $db = null;

    public function setDatabase(Database $db): void
    {
        $this->db = $db;
    }

    /**
     * Run all migrations
     */
    public function run(): void
    {
        if (!$this->db) {
            throw new \RuntimeException("Database connection not established for migration.");
        }

        $prefix = $_ENV['DB_PREFIX'] ?? 'nuke_';

        $queries = [
            // 1. Users Table
            "CREATE TABLE IF NOT EXISTS `{$prefix}users` (
                `user_id` INT AUTO_INCREMENT PRIMARY KEY,
                `username` VARCHAR(50) NOT NULL UNIQUE,
                `user_email` VARCHAR(100) NOT NULL UNIQUE,
                `user_password` VARCHAR(255) NOT NULL,
                `user_level` TINYINT DEFAULT 1,
                `created_at` INT NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            // 2. News/Stories Table
            "CREATE TABLE IF NOT EXISTS `{$prefix}stories` (
                `sid` INT AUTO_INCREMENT PRIMARY KEY,
                `aid` VARCHAR(50) NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `time` INT NOT NULL,
                `hometext` TEXT,
                `bodytext` TEXT,
                `topic` INT DEFAULT 1,
                `informant` VARCHAR(50)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            // 3. Blocks/Widgets Table
            "CREATE TABLE IF NOT EXISTS `{$prefix}blocks` (
                `bid` INT AUTO_INCREMENT PRIMARY KEY,
                `bposition` CHAR(1) NOT NULL DEFAULT 'l',
                `title` VARCHAR(100) NOT NULL,
                `content` TEXT,
                `blockfile` VARCHAR(255),
                `active` TINYINT DEFAULT 1,
                `weight` INT DEFAULT 0
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            // 4. Media Table
            "CREATE TABLE IF NOT EXISTS `{$prefix}media` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `filename` VARCHAR(255) NOT NULL,
                `path` VARCHAR(255) NOT NULL,
                `mime_type` VARCHAR(50),
                `size` INT,
                `uploader_id` INT,
                `created_at` INT NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            // 5. Config Table
            "CREATE TABLE IF NOT EXISTS `{$prefix}config` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `sitename` VARCHAR(255) DEFAULT 'FtoniNuke Portal',
                `theme` VARCHAR(50) DEFAULT 'ftoni_modern',
                `api_key` VARCHAR(100)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            // 6. Admin Log
            "CREATE TABLE IF NOT EXISTS `{$prefix}admin_log` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `admin_id` INT,
                `action` VARCHAR(50),
                `module` VARCHAR(50),
                `details` TEXT,
                `ip_address` VARCHAR(45),
                `created_at` INT NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
        ];

        foreach ($queries as $sql) {
            $this->db->query($sql);
        }
    }
}
