<?php

declare(strict_types=1);

namespace FtoniNuke\Repository;

use FtoniNuke\Core\Database;

class PageRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(bool $onlyActive = false): array
    {
        $where = $onlyActive ? 'WHERE active = 1' : '';
        return $this->db->fetchAll(
            "SELECT * FROM nuke_pages $where ORDER BY sort_order ASC, id ASC"
        );
    }

    public function allInMenu(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM nuke_pages WHERE active = 1 AND show_in_menu = 1 ORDER BY sort_order ASC, id ASC"
        );
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM nuke_pages WHERE id = ? LIMIT 1", [$id]);
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM nuke_pages WHERE slug = ? AND active = 1 LIMIT 1",
            [$slug]
        );
    }

    public function slugExists(string $slug, int $exceptId = 0): bool
    {
        $row = $this->db->fetch(
            "SELECT id FROM nuke_pages WHERE slug = ? AND id != ? LIMIT 1",
            [$slug, $exceptId]
        );
        return $row !== null;
    }

    public function create(array $data): int
    {
        $this->db->query(
            "INSERT INTO nuke_pages (title, slug, content, template, meta_description, active, show_in_menu, sort_order, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['title'],
                $data['slug'],
                $data['content'] ?? '',
                $data['template'] ?? 'default',
                $data['meta_description'] ?? '',
                (int)($data['active'] ?? 1),
                (int)($data['show_in_menu'] ?? 0),
                (int)($data['sort_order'] ?? 0),
                time(),
            ]
        );
        return (int)$this->db->getConnection()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $this->db->query(
            "UPDATE nuke_pages
             SET title = ?, slug = ?, content = ?, template = ?, meta_description = ?, active = ?, show_in_menu = ?, sort_order = ?, updated_at = ?
             WHERE id = ?",
            [
                $data['title'],
                $data['slug'],
                $data['content'] ?? '',
                $data['template'] ?? 'default',
                $data['meta_description'] ?? '',
                (int)($data['active'] ?? 1),
                (int)($data['show_in_menu'] ?? 0),
                (int)($data['sort_order'] ?? 0),
                time(),
                $id,
            ]
        );
        return true;
    }

    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM nuke_pages WHERE id = ?", [$id]);
        return true;
    }

    public function toggleActive(int $id): bool
    {
        $row = $this->findById($id);
        if (!$row) return false;
        $new = $row['active'] ? 0 : 1;
        $this->db->query("UPDATE nuke_pages SET active = ? WHERE id = ?", [$new, $id]);
        return true;
    }

    public function makeUniqueSlug(string $title, int $exceptId = 0): string
    {
        $base = \FtoniNuke\Helper\SlugHelper::slugify($title);
        if ($base === 'n-a') $base = 'sayfa';

        $slug = $base;
        $i = 2;
        while ($this->slugExists($slug, $exceptId)) {
            $slug = $base . '-' . $i;
            $i++;
            if ($i > 100) break;
        }
        return $slug;
    }

    // ─── Contact mesajları ─────────────────────────────
    public function saveContactMessage(array $data): int
    {
        $this->db->query(
            "INSERT INTO nuke_contact_messages (name, email, subject, message, ip_address, created_at)
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $data['name'],
                $data['email'],
                $data['subject'] ?? '',
                $data['message'],
                $data['ip_address'] ?? '',
                time(),
            ]
        );
        return (int)$this->db->getConnection()->lastInsertId();
    }

    public function allContactMessages(int $limit = 100): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM nuke_contact_messages ORDER BY created_at DESC LIMIT " . (int)$limit
        );
    }

    public function countUnreadMessages(): int
    {
        $row = $this->db->fetch("SELECT COUNT(*) AS c FROM nuke_contact_messages WHERE is_read = 0");
        return (int)($row['c'] ?? 0);
    }

    public function markMessageRead(int $id): void
    {
        $this->db->query("UPDATE nuke_contact_messages SET is_read = 1 WHERE id = ?", [$id]);
    }

    public function deleteContactMessage(int $id): void
    {
        $this->db->query("DELETE FROM nuke_contact_messages WHERE id = ?", [$id]);
    }
}
