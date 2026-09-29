<?php

declare(strict_types=1);

namespace FtoniNuke\Repository;

use FtoniNuke\Core\Database;

class CommentRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Bir habere ait onaylı yorumlar (threaded tree) */
    public function byStory(int $sid, bool $onlyApproved = true): array
    {
        $where = $onlyApproved ? 'AND active = 1' : '';
        $rows = $this->db->fetchAll(
            "SELECT * FROM nuke_comments
             WHERE sid = ? $where
             ORDER BY created_at ASC",
            [$sid]
        );
        return $this->buildTree($rows, 0);
    }

    /** Tüm yorumlar (admin için) */
    public function all(string $filter = 'all', int $limit = 100, int $offset = 0): array
    {
        $where = match ($filter) {
            'pending' => 'WHERE active = 0',
            'approved' => 'WHERE active = 1',
            default   => '',
        };
        return $this->db->fetchAll(
            "SELECT c.*, s.title AS story_title
             FROM nuke_comments c
             LEFT JOIN nuke_stories s ON s.sid = c.sid
             $where
             ORDER BY c.created_at DESC
             LIMIT " . (int)$limit . " OFFSET " . (int)$offset
        );
    }

    /** Bekleyen yorum sayısı */
    public function countPending(): int
    {
        $row = $this->db->fetch("SELECT COUNT(*) AS c FROM nuke_comments WHERE active = 0");
        return (int)($row['c'] ?? 0);
    }

    public function countByStory(int $sid): int
    {
        $row = $this->db->fetch(
            "SELECT COUNT(*) AS c FROM nuke_comments WHERE sid = ? AND active = 1",
            [$sid]
        );
        return (int)($row['c'] ?? 0);
    }

    public function findById(int $cid): ?array
    {
        return $this->db->fetch("SELECT * FROM nuke_comments WHERE cid = ? LIMIT 1", [$cid]);
    }

    public function create(array $data): int
    {
        $this->db->query(
            "INSERT INTO nuke_comments
             (sid, pid, author_name, author_email, author_id, author_ip, content, active, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                (int)$data['sid'],
                (int)($data['pid'] ?? 0),
                $data['author_name'],
                $data['author_email'] ?? '',
                (int)($data['author_id'] ?? 0),
                $data['author_ip'] ?? '',
                $data['content'],
                (int)($data['active'] ?? 0),
                time(),
            ]
        );
        return (int)$this->db->getConnection()->lastInsertId();
    }

    public function approve(int $cid): bool
    {
        $this->db->query("UPDATE nuke_comments SET active = 1 WHERE cid = ?", [$cid]);
        return true;
    }

    public function unapprove(int $cid): bool
    {
        $this->db->query("UPDATE nuke_comments SET active = 0 WHERE cid = ?", [$cid]);
        return true;
    }

    public function delete(int $cid): bool
    {
        // Alt yorumları da sil
        $this->db->query("DELETE FROM nuke_comments WHERE pid = ?", [$cid]);
        $this->db->query("DELETE FROM nuke_comments WHERE cid = ?", [$cid]);
        return true;
    }

    /** Son X dakika içinde bir IP'den kaç yorum geldi */
    public function countByIp(string $ip, int $minutes = 60): int
    {
        $since = time() - ($minutes * 60);
        $row = $this->db->fetch(
            "SELECT COUNT(*) AS c FROM nuke_comments
             WHERE author_ip = ? AND created_at >= ?",
            [$ip, $since]
        );
        return (int)($row['c'] ?? 0);
    }

    /** Aynı içerik son X dakika içinde gönderildi mi? */
    public function isDuplicate(string $content, string $ip, int $minutes = 10): bool
    {
        $since = time() - ($minutes * 60);
        $row = $this->db->fetch(
            "SELECT COUNT(*) AS c FROM nuke_comments
             WHERE author_ip = ? AND content = ? AND created_at >= ?",
            [$ip, $content, $since]
        );
        return (int)($row['c'] ?? 0) > 0;
    }

    // ─── Ban yönetimi ─────────────────────────────────
    public function isBanned(string $ip): bool
    {
        $row = $this->db->fetch("SELECT id FROM nuke_comment_bans WHERE ip_address = ? LIMIT 1", [$ip]);
        return $row !== null;
    }

    public function banIp(string $ip, string $reason = ''): void
    {
        if ($this->isBanned($ip)) return;
        $this->db->query(
            "INSERT INTO nuke_comment_bans (ip_address, reason, created_at) VALUES (?, ?, ?)",
            [$ip, $reason, time()]
        );
    }

    public function unbanIp(string $ip): void
    {
        $this->db->query("DELETE FROM nuke_comment_bans WHERE ip_address = ?", [$ip]);
    }

    public function bannedIps(): array
    {
        return $this->db->fetchAll("SELECT * FROM nuke_comment_bans ORDER BY created_at DESC");
    }

    // ─── Tree builder ─────────────────────────────────
    private function buildTree(array $rows, int $parentId): array
    {
        $branch = [];
        foreach ($rows as $r) {
            if ((int)$r['pid'] === $parentId) {
                $children = $this->buildTree($rows, (int)$r['cid']);
                if (!empty($children)) {
                    $r['children'] = $children;
                }
                $branch[] = $r;
            }
        }
        return $branch;
    }
}
