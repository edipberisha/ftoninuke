<?php

declare(strict_types=1);

namespace FtoniNuke\Service;

use FtoniNuke\Core\Database;

/**
 * AdminLogService - Tracks administrative actions for security audits.
 */
class AdminLogService
{
    private Database $db;
    private string $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->table = ($_ENV['DB_PREFIX'] ?? 'nuke_') . 'admin_log';
    }

    /**
     * Record an admin action
     */
    public function log(int $adminId, string $action, string $module, string $details = ''): void
    {
        $sql = "INSERT INTO {$this->table} (admin_id, action, module, details, ip_address, created_at) 
                VALUES (:admin_id, :action, :module, :details, :ip, :now)";
        
        $this->db->query($sql, [
            'admin_id' => $adminId,
            'action'   => $action,
            'module'   => $module,
            'details'  => $details,
            'ip'       => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            'now'      => time()
        ]);
    }

    /**
     * Get recent logs
     */
    public function getRecent(int $limit = 20): array
    {
        $sql = "SELECT l.*, u.username 
                FROM {$this->table} l
                JOIN " . ($_ENV['DB_PREFIX'] ?? 'nuke_') . "users u ON u.user_id = l.admin_id
                ORDER BY l.id DESC LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
}
