<?php

declare(strict_types=1);

namespace FtoniNuke\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Modern Database Wrapper for FtoniNuke
 *
 * Replaces legacy mysql_ and sql_ functions with a secure PDO implementation.
 * Uses Prepared Statements to prevent SQL Injection.
 *
 * Configuration önceliği:
 *   1) config/database.php  (kurulum sihirbazının yazdığı dosya)
 *   2) $_ENV değişkenleri   (.env / docker environment)
 *   3) Güvenli default değerler
 */
class Database
{
    private static ?self $instance = null;
    private PDO $connection;

    private function __construct()
    {
        // 1) config/database.php varsa oradan oku
        $configPath = __DIR__ . '/../../config/database.php';

        if (is_file($configPath)) {
            $config = require $configPath;

            $host = $config['host']   ?? 'database-server-db-1';
            $db   = $config['dbname'] ?? 'ftoninuke_db';
            $user = $config['user']   ?? 'edip';
            $pass = $config['pass']   ?? '19*-+Edip*-+75';
        } else {
            // 2) $_ENV'den oku, yoksa 3) default değerler
            $host = $_ENV['DB_HOST'] ?? 'database-server-db-1';
            $db   = $_ENV['DB_NAME'] ?? 'ftoninuke_db';
            $user = $_ENV['DB_USER'] ?? 'edip';
            $pass = $_ENV['DB_PASS'] ?? '19*-+Edip*-+75';
        }

        $port    = $_ENV['DB_PORT'] ?? '3306';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            throw new RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Executes a query with parameters (Prepared Statements)
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            // In production, log this instead of throwing
            throw new RuntimeException("Query error: " . $e->getMessage());
        }
    }

    /**
     * Fetch a single row
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        return $this->query($sql, $params)->fetch() ?: null;
    }

    /**
     * Fetch all rows
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Get the underlying PDO connection
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}