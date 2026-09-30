<?php

declare(strict_types=1);

namespace FtoniNuke\Repository;

use FtoniNuke\Core\Database;

class UserRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(string $orderBy = 'user_id DESC'): array
    {
        return $this->db->fetchAll(
            "SELECT user_id, username, user_email, user_level, created_at
             FROM nuke_users
             ORDER BY $orderBy"
        );
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM nuke_users WHERE user_id = ? LIMIT 1",
            [$id]
        );
    }

    public function findByUsername(string $username): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM nuke_users WHERE username = ? LIMIT 1",
            [$username]
        );
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM nuke_users WHERE user_email = ? LIMIT 1",
            [$email]
        );
    }

    public function usernameExists(string $username, int $exceptId = 0): bool
    {
        $row = $this->db->fetch(
            "SELECT user_id FROM nuke_users WHERE username = ? AND user_id != ? LIMIT 1",
            [$username, $exceptId]
        );
        return $row !== null;
    }

    public function emailExists(string $email, int $exceptId = 0): bool
    {
        $row = $this->db->fetch(
            "SELECT user_id FROM nuke_users WHERE user_email = ? AND user_id != ? LIMIT 1",
            [$email, $exceptId]
        );
        return $row !== null;
    }

    public function create(array $data): int
    {
        $this->db->query(
            "INSERT INTO nuke_users (username, user_email, user_password, user_level, created_at)
             VALUES (?, ?, ?, ?, ?)",
            [
                $data['username'],
                $data['user_email'],
                password_hash($data['password'], PASSWORD_BCRYPT),
                (int)($data['user_level'] ?? 1),
                time(),
            ]
        );
        return (int)$this->db->getConnection()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [
            'username'    => $data['username'],
            'user_email'  => $data['user_email'],
            'user_level'  => (int)($data['user_level'] ?? 1),
        ];

        if (!empty($data['password'])) {
            $fields['user_password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $sets = [];
        $values = [];
        foreach ($fields as $k => $v) {
            $sets[] = "$k = ?";
            $values[] = $v;
        }
        $values[] = $id;

        $this->db->query(
            "UPDATE nuke_users SET " . implode(', ', $sets) . " WHERE user_id = ?",
            $values
        );
        return true;
    }

    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM nuke_users WHERE user_id = ?", [$id]);
        return true;
    }

    public function updatePassword(int $id, string $newPassword): bool
    {
        $this->db->query(
            "UPDATE nuke_users SET user_password = ? WHERE user_id = ?",
            [password_hash($newPassword, PASSWORD_BCRYPT), $id]
        );
        return true;
    }

    public function count(): int
    {
        $row = $this->db->fetch("SELECT COUNT(*) AS c FROM nuke_users");
        return (int)($row['c'] ?? 0);
    }

    public function countByLevel(int $level): int
    {
        $row = $this->db->fetch(
            "SELECT COUNT(*) AS c FROM nuke_users WHERE user_level = ?",
            [$level]
        );
        return (int)($row['c'] ?? 0);
    }
}
