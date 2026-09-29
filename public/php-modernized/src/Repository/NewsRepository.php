<?php

declare(strict_types=1);

namespace FtoniNuke\Repository;

use FtoniNuke\Core\Database;

class NewsRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Yayınlanan tüm haberleri getir (ana sayfa)
     */
    public function all(int $limit = 20, int $offset = 0): array
    {
        return $this->db->fetchAll(
            "SELECT s.*, u.username AS author_name, t.topic_name
             FROM nuke_stories s
             LEFT JOIN nuke_users u ON u.username = s.aid
             LEFT JOIN nuke_topics t ON t.topic_id = s.topic
             ORDER BY s.time DESC
             LIMIT " . (int)$limit . " OFFSET " . (int)$offset
        );
    }

    /**
     * Toplam haber sayısı (sayfalama için)
     */
    public function count(): int
    {
        $row = $this->db->fetch("SELECT COUNT(*) AS c FROM nuke_stories");
        return (int)($row['c'] ?? 0);
    }

    /**
     * Tek haber bul
     */
    public function findById(int $sid): ?array
    {
        return $this->db->fetch(
            "SELECT s.*, u.username AS author_name, t.topic_name
             FROM nuke_stories s
             LEFT JOIN nuke_users u ON u.username = s.aid
             LEFT JOIN nuke_topics t ON t.topic_id = s.topic
             WHERE s.sid = ? LIMIT 1",
            [$sid]
        );
    }

    /**
     * Slug ile haber bul
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetch(
            "SELECT s.*, u.username AS author_name, t.topic_name
             FROM nuke_stories s
             LEFT JOIN nuke_users u ON u.username = s.aid
             LEFT JOIN nuke_topics t ON t.topic_id = s.topic
             WHERE s.slug = ? LIMIT 1",
            [$slug]
        );
    }

    /**
     * Aynı slug var mı? (edit'te kendi sid'ini hariç tut)
     */
    public function slugExists(string $slug, int $exceptSid = 0): bool
    {
        $row = $this->db->fetch(
            "SELECT sid FROM nuke_stories WHERE slug = ? AND sid != ? LIMIT 1",
            [$slug, $exceptSid]
        );
        return $row !== null;
    }

    /**
     * Unique slug üret (aynı isimden varsa sonuna -2, -3 ekler)
     */
    public function makeUniqueSlug(string $title, int $exceptSid = 0): string
    {
        $base = \FtoniNuke\Helper\SlugHelper::slugify($title);
        if ($base === 'n-a') $base = 'haber';

        $slug = $base;
        $i    = 2;
        while ($this->slugExists($slug, $exceptSid)) {
            $slug = $base . '-' . $i;
            $i++;
            if ($i > 100) {
                $slug = $base . '-' . time();
                break;
            }
        }
        return $slug;
    }
	
    /**
     * Yeni haber oluştur
     */
    public function create(array $data): int
    {
        // Slug otomatik üret
        if (empty($data['slug'])) {
            $data['slug'] = $this->makeUniqueSlug($data['title']);
        }

        $this->db->query(
            "INSERT INTO nuke_stories (title, slug, image, hometext, bodytext, aid, time, topic, informant)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['title'],
                $data['slug'],
                $data['image'] ?? '',
                $data['hometext'] ?? '',
                $data['bodytext'] ?? '',
                $data['aid'],
                time(),
                (int)($data['topic'] ?? 1),
                $data['informant'] ?? '',
            ]
        );
        return (int)$this->db->getConnection()->lastInsertId();
    }

    public function update(int $sid, array $data): bool
    {
        // Slug değişmişse yeniden üret
        if (empty($data['slug'])) {
            $data['slug'] = $this->makeUniqueSlug($data['title'], $sid);
        }

        $this->db->query(
            "UPDATE nuke_stories
             SET title = ?, slug = ?, image = ?, hometext = ?, bodytext = ?, topic = ?, informant = ?
             WHERE sid = ?",
            [
                $data['title'],
                $data['slug'],
                $data['image'] ?? '',
                $data['hometext'] ?? '',
                $data['bodytext'] ?? '',
                (int)($data['topic'] ?? 1),
                $data['informant'] ?? '',
                $sid,
            ]
        );
        return true;
    }
	
	    /**
     * Sonraki haber (daha yeni tarihli)
     */
    public function findNext(int $sid, int $time): ?array
    {
        return $this->db->fetch(
            "SELECT sid, title, image, slug FROM nuke_stories
             WHERE time > ? OR (time = ? AND sid > ?)
             ORDER BY time ASC, sid ASC
             LIMIT 1",
            [$time, $time, $sid]
        );
    }

    /**
     * Önceki haber (daha eski tarihli)
     */
    public function findPrev(int $sid, int $time): ?array
    {
        return $this->db->fetch(
            "SELECT sid, title, image, slug FROM nuke_stories
             WHERE time < ? OR (time = ? AND sid < ?)
             ORDER BY time DESC, sid DESC
             LIMIT 1",
            [$time, $time, $sid]
        );
    }

    /**
     * Haber sil
     */
    public function delete(int $sid): bool
    {
        $this->db->query("DELETE FROM nuke_stories WHERE sid = ?", [$sid]);
        return true;
    }
}