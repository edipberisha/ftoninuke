<?php

declare(strict_types=1);

namespace FtoniNuke\Repository;

use FtoniNuke\Core\Database;

class MenuRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Tüm öğeler (admin için) */
    public function all(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM nuke_menu ORDER BY parent_id ASC, sort_order ASC, id ASC"
        );
    }

    /** Aktif öğeler (frontend için) */
    public function allActive(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM nuke_menu WHERE active = 1 ORDER BY parent_id ASC, sort_order ASC, id ASC"
        );
    }

    /** Hiyerarşik ağaç */
    public function allHierarchical(): array
    {
        $rows = $this->allActive();
        return $this->buildTree($rows, 0);
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM nuke_menu WHERE id = ? LIMIT 1", [$id]);
    }

    public function create(array $data): int
    {
        $this->db->query(
            "INSERT INTO nuke_menu (title, url, target, parent_id, sort_order, active, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $data['title'],
                $data['url'],
                $data['target'] ?? '_self',
                (int)($data['parent_id'] ?? 0),
                (int)($data['sort_order'] ?? 0),
                (int)($data['active'] ?? 1),
                time(),
            ]
        );
        return (int)$this->db->getConnection()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $this->db->query(
            "UPDATE nuke_menu SET title = ?, url = ?, target = ?, parent_id = ?, sort_order = ?, active = ?
             WHERE id = ?",
            [
                $data['title'],
                $data['url'],
                $data['target'] ?? '_self',
                (int)($data['parent_id'] ?? 0),
                (int)($data['sort_order'] ?? 0),
                (int)($data['active'] ?? 1),
                $id,
            ]
        );
        return true;
    }

    public function delete(int $id): bool
    {
        // Alt öğeleri de sil
        $this->db->query("DELETE FROM nuke_menu WHERE parent_id = ?", [$id]);
        $this->db->query("DELETE FROM nuke_menu WHERE id = ?", [$id]);
        return true;
    }

    public function toggleActive(int $id): bool
    {
        $row = $this->findById($id);
        if (!$row) return false;
        $new = $row['active'] ? 0 : 1;
        $this->db->query("UPDATE nuke_menu SET active = ? WHERE id = ?", [$new, $id]);
        return true;
    }

    private function buildTree(array $items, int $parentId): array
    {
        $branch = [];
        foreach ($items as $item) {
            if ((int)$item['parent_id'] === $parentId) {
                $children = $this->buildTree($items, (int)$item['id']);
                if (!empty($children)) {
                    $item['children'] = $children;
                }
                $branch[] = $item;
            }
        }
        return $branch;
    }
}
