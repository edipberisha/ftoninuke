<?php

declare(strict_types=1);

namespace FtoniNuke\Repository;

use FtoniNuke\Core\Database;

/**
 * UserRepository - Handles all database operations for nuke_users table.
 */
class UserRepository
{
    private Database $db;
    private string $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->table = ($_ENV['DB_PREFIX'] ?? 'nuke_') . 'users';
    }

    /**
     * Find a user by username
     */
    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        return $this->db->fetch($sql, ['username' => $username]);
    }

    /**
     * Find a user by ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    /**
     * Update user password with modern hash
     */
    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        $sql = "UPDATE {$this->table} SET user_password = :pass WHERE user_id = :id";
        return (bool) $this->db->query($sql, [
            'pass' => $hashedPassword,
            'id'   => $userId
        ]);
    }

    /**
     * Update user profile information
     */
    public function updateProfile(int $userId, array $data): bool
    {
        $allowedFields = ['user_email', 'name', 'user_occ', 'user_from'];
        $updates = [];
        $params = ['id' => $userId];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $updates[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE user_id = :id";
        return (bool) $this->db->query($sql, $params);
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(int $userId): void
    {
        // Legacy Nuke used 'user_lastvisit', let's stick to it or migrate
        $sql = "UPDATE {$this->table} SET user_lastvisit = :now WHERE user_id = :id";
        $this->db->query($sql, [
            'now' => time(),
            'id'  => $userId
        ]);
    }
}
