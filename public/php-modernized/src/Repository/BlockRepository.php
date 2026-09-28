<?php

declare(strict_types=1);

namespace FtoniNuke\Repository;

use FtoniNuke\Core\Database;

class BlockRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM nuke_blocks ORDER BY bposition ASC, weight ASC, bid ASC"
        );
    }

    public function byPosition(string $pos): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM nuke_blocks WHERE bposition = ? AND active = 1 ORDER BY weight ASC, bid ASC",
            [$pos]
        );
    }

    public function findById(int $bid): ?array
    {
        return $this->db->fetch("SELECT * FROM nuke_blocks WHERE bid = ? LIMIT 1", [$bid]);
    }

    public function create(array $data): int
    {
        $this->db->query(
            "INSERT INTO nuke_blocks (bposition, title, block_type, content, image, link_url, config, blockfile, weight, active, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['bposition']  ?? 'r',
                $data['title']      ?? '',
                $data['block_type'] ?? 'html',
                $data['content']    ?? '',
                $data['image']      ?? '',
                $data['link_url']   ?? '',
                $data['config']     ?? '',
                $data['blockfile']  ?? '',
                (int)($data['weight'] ?? 0),
                (int)($data['active'] ?? 1),
                time(),
            ]
        );
        return (int)$this->db->getConnection()->lastInsertId();
    }

    public function update(int $bid, array $data): bool
    {
        $this->db->query(
            "UPDATE nuke_blocks
             SET bposition = ?, title = ?, block_type = ?, content = ?, image = ?, link_url = ?, config = ?, blockfile = ?, weight = ?, active = ?
             WHERE bid = ?",
            [
                $data['bposition']  ?? 'r',
                $data['title']      ?? '',
                $data['block_type'] ?? 'html',
                $data['content']    ?? '',
                $data['image']      ?? '',
                $data['link_url']   ?? '',
                $data['config']     ?? '',
                $data['blockfile']  ?? '',
                (int)($data['weight'] ?? 0),
                (int)($data['active'] ?? 1),
                $bid,
            ]
        );
        return true;
    }

    public function delete(int $bid): bool
    {
        $this->db->query("DELETE FROM nuke_blocks WHERE bid = ?", [$bid]);
        return true;
    }

    public function toggleActive(int $bid): bool
    {
        $row = $this->findById($bid);
        if (!$row) return false;
        $new = $row['active'] ? 0 : 1;
        $this->db->query("UPDATE nuke_blocks SET active = ? WHERE bid = ?", [$new, $bid]);
        return true;
    }

    /**
     * Geliştirici dostu: src/Blocks/*.php dosyalarını tara
     */
    public function availableBlockFiles(): array
    {
        $dir = __DIR__ . '/../Blocks';
        if (!is_dir($dir)) return [];

        $files = [];
        foreach (scandir($dir) as $f) {
            if ($f === '.' || $f === '..') continue;
            if (substr($f, -4) !== '.php') continue;
            $files[] = [
                'file'  => $f,
                'name'  => basename($f, '.php'),
            ];
        }
        return $files;
    }
}
