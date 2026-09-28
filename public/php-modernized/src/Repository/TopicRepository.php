<?php

declare(strict_types=1);

namespace FtoniNuke\Repository;

use FtoniNuke\Core\Database;

class TopicRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        return $this->db->fetchAll("SELECT * FROM nuke_topics ORDER BY topic_name ASC");
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM nuke_topics WHERE topic_id = ? LIMIT 1",
            [$id]
        );
    }

    public function create(string $name, string $description = '', string $image = ''): int
    {
        $slug = $this->makeSlug($name);
        $this->db->query(
            "INSERT INTO nuke_topics (topic_name, topic_slug, topic_description, topic_image, created_at)
             VALUES (?, ?, ?, ?, ?)",
            [$name, $slug, $description, $image, time()]
        );
        return (int)$this->db->getConnection()->lastInsertId();
    }

    public function update(int $id, string $name, string $description = '', string $image = ''): bool
    {
        $this->db->query(
            "UPDATE nuke_topics
             SET topic_name = ?, topic_description = ?, topic_image = ?
             WHERE topic_id = ?",
            [$name, $description, $image, $id]
        );
        return true;
    }

    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM nuke_topics WHERE topic_id = ?", [$id]);
        return true;
    }

    private function makeSlug(string $text): string
    {
        $tr = ['ç','ğ','ı','i','ö','ş','ü','Ç','Ğ','İ','Ö','Ş','Ü'];
        $en = ['c','g','i','i','o','s','u','c','g','i','o','s','u'];
        $text = str_replace($tr, $en, $text);
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');
        return $text . '-' . substr(md5((string)time()), 0, 4);
    }
}
