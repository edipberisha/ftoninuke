<?php

declare(strict_types=1);

namespace FtoniNuke\Repository;

use FtoniNuke\Core\Database;

/**
 * MediaRepository - Handles persistence for the centralized media library.
 */
class MediaRepository
{
    private Database $db;
    private string $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->table = ($_ENV['DB_PREFIX'] ?? 'nuke_') . 'media';
    }

    /**
     * Save media metadata
     */
    public function save(array $data): int
    {
        $sql = "INSERT INTO {$this->table} (filename, path, mime_type, size, uploader_id, created_at) 
                VALUES (:filename, :path, :mime, :size, :uploader, :now)";
        
        $this->db->query($sql, [
            'filename' => $data['filename'],
            'path'     => $data['path'],
            'mime'     => $data['mime_type'],
            'size'     => $data['size'],
            'uploader' => $data['uploader_id'],
            'now'      => time()
        ]);

        return (int)$this->db->getConnection()->lastInsertId();
    }

    /**
     * Get all media files
     */
    public function getAll(int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT m.*, u.username 
                FROM {$this->table} m
                LEFT JOIN " . ($_ENV['DB_PREFIX'] ?? 'nuke_') . "users u ON u.user_id = m.uploader_id
                ORDER BY m.id DESC LIMIT :limit OFFSET :offset";
        
        return $this->db->fetchAll($sql, [
            'limit'  => $limit,
            'offset' => $offset
        ]);
    }

    /**
     * Delete media record
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return (bool)$this->db->query($sql, ['id' => $id]);
    }

    /**
     * Find by ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
}
