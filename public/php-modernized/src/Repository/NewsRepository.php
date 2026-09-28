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
     * Yeni haber oluştur
     */
    public function create(array $data): int
    {
        $this->db->query(
            "INSERT INTO nuke_stories (title, image, hometext, bodytext, aid, time, topic, informant)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['title'],
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

    /**
     * Haber güncelle
     */
    public function update(int $sid, array $data): bool
    {
        $this->db->query(
            "UPDATE nuke_stories
             SET title = ?, image = ?, hometext = ?, bodytext = ?, topic = ?, informant = ?
             WHERE sid = ?",
            [
                $data['title'],
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
            "SELECT sid, title, image FROM nuke_stories
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
            "SELECT sid, title, image FROM nuke_stories
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