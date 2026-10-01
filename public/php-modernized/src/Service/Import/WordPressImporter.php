<?php

declare(strict_types=1);

namespace FtoniNuke\Service\Import;

use FtoniNuke\Helper\SlugHelper;

/**
 * WordPress Importer
 *
 * WordPress veritabanından FtoniNuke'a veri aktarır.
 *
 * Beklenen tablolar (WP standart):
 *   - wp_posts (ID, post_author, post_date, post_content, post_title,
 *               post_excerpt, post_status, post_type, post_name)
 *   - wp_users (ID, user_login, user_email, user_pass, user_registered,
 *               display_name)
 *   - wp_terms (term_id, name, slug)
 *   - wp_term_taxonomy (term_taxonomy_id, term_id, taxonomy)
 *   - wp_term_relationships (object_id, term_taxonomy_id)
 *   - wp_comments (comment_ID, comment_post_ID, comment_author,
 *                  comment_author_email, comment_content, comment_date,
 *                  comment_approved)
 */
class WordPressImporter extends ImportService
{
    protected string $sourceName = 'wordpress';
    protected string $prefix = 'wp_';

    /** @var array WP post ID → FtoniNuke sid eşlemesi */
    protected array $postMap = [];

    /** @var array WP term_id → FtoniNuke topic_id eşlemesi */
    protected array $termMap = [];

    public function __construct(string $prefix = 'wp_')
    {
        parent::__construct();
        $this->prefix = $prefix;
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * Ön kontrol
     */
    public function preflight(): array
    {
        $required = ['posts', 'users'];
        $optional = ['terms', 'term_taxonomy', 'term_relationships', 'comments'];

        $report = ['required' => [], 'optional' => [], 'ok' => true];

        foreach ($required as $t) {
            $table = $this->prefix . $t;
            $exists = $this->tableExists($table);
            $report['required'][$table] = $exists;
            if (!$exists) $report['ok'] = false;
        }

        foreach ($optional as $t) {
            $table = $this->prefix . $t;
            $report['optional'][$table] = $this->tableExists($table);
        }

        return $report;
    }

    // ═══════════════════════════════════════════════════
    // KATEGORİLER
    // ═══════════════════════════════════════════════════
    public function importCategories(): array
    {
        $step = 'categories';
        $this->startStep($step);

        $termsTable = $this->prefix . 'terms';
        $taxTable   = $this->prefix . 'term_taxonomy';

        if (!$this->tableExists($termsTable) || !$this->tableExists($taxTable)) {
            $this->warnings[] = "Tablo yok: $termsTable veya $taxTable";
            return $this->stepReport($step);
        }

        // Sadece 'category' taxonomy'sini al
        $sql = "SELECT t.term_id, t.name, t.slug, tt.description, tt.parent
                FROM `$termsTable` t
                INNER JOIN `$taxTable` tt ON tt.term_id = t.term_id
                WHERE tt.taxonomy = 'category'
                ORDER BY t.name ASC";

        $rows = $this->sourceDb->query($sql)->fetchAll();
        $this->stats[$step]['total'] = count($rows);
        $this->logStart($step, count($rows));

        foreach ($rows as $row) {
            $name = trim($row['name'] ?? '');
            if ($name === '') {
                $this->updateStats($step, 'skipped');
                continue;
            }

            // Aynı isim var mı?
            $existing = $this->targetDb->fetch(
                "SELECT topic_id FROM nuke_topics WHERE topic_name = ? LIMIT 1",
                [$name]
            );
            if ($existing) {
                $this->termMap[(int)$row['term_id']] = (int)$existing['topic_id'];
                $this->updateStats($step, 'skipped');
                continue;
            }

            $slug = SlugHelper::slugify($name);
            if ($slug === 'n-a') $slug = 'topic';
            $baseSlug = $slug;
            $i = 2;
            while ($this->targetDb->fetch("SELECT topic_id FROM nuke_topics WHERE topic_slug = ? LIMIT 1", [$slug])) {
                $slug = $baseSlug . '-' . $i++;
                if ($i > 100) break;
            }

            if ($this->dryRun) {
                $this->termMap[(int)$row['term_id']] = 0;
                $this->updateStats($step, 'imported');
                continue;
            }

            try {
                $this->targetDb->query(
                    "INSERT INTO nuke_topics (topic_name, topic_slug, topic_description, created_at)
                     VALUES (?, ?, ?, ?)",
                    [$name, $slug, trim($row['description'] ?? ''), time()]
                );
                $newId = (int)$this->targetDb->getConnection()->lastInsertId();
                $this->termMap[(int)$row['term_id']] = $newId;
                $this->updateStats($step, 'imported');
            } catch (\Throwable $e) {
                $this->updateStats($step, 'failed');
                $this->errors[] = "Kategori '$name': " . $e->getMessage();
            }
        }

        $this->finishStep($step);
        $this->logFinish($step);
        return $this->stepReport($step);
    }

    // ═══════════════════════════════════════════════════
    // KULLANICILAR
    // ═══════════════════════════════════════════════════
    public function importUsers(): array
    {
        $step = 'users';
        $this->startStep($step);

        $table = $this->prefix . 'users';
        if (!$this->tableExists($table)) {
            $this->warnings[] = "Tablo yok: $table";
            return $this->stepReport($step);
        }

        // usermeta varsa rol bilgisini de çek
        $metaTable = $this->prefix . 'usermeta';
        $hasMeta = $this->tableExists($metaTable);

        $sql = "SELECT ID, user_login, user_email, user_pass, user_registered, display_name
                FROM `$table` ORDER BY ID ASC";
        $rows = $this->sourceDb->query($sql)->fetchAll();
        $this->stats[$step]['total'] = count($rows);
        $this->logStart($step, count($rows));

        foreach ($rows as $row) {
            $username = trim($row['user_login'] ?? '');
            $email    = trim($row['user_email'] ?? '');
            $password = (string)($row['user_pass'] ?? '');
            $regdate  = strtotime($row['user_registered'] ?? 'now') ?: time();

            if ($username === '') {
                $this->updateStats($step, 'skipped');
                continue;
            }

            // Rol belirle
            $level = 0;
            if ($hasMeta) {
                $metaSql = "SELECT meta_value FROM `$metaTable`
                            WHERE user_id = ? AND meta_key = ?
                            LIMIT 1";
                $stmt = $this->sourceDb->prepare($metaSql);
                $stmt->execute([(int)$row['ID'], $this->prefix . 'capabilities']);
                $caps = $stmt->fetchColumn();

                if ($caps) {
                    if (strpos($caps, 'administrator') !== false) {
                        $level = 2;
                    } elseif (strpos($caps, 'editor') !== false || strpos($caps, 'author') !== false) {
                        $level = 1;
                    }
                }
            }

            // Zaten var mı?
            $existing = $this->targetDb->fetch(
                "SELECT user_id FROM nuke_users WHERE username = ? OR user_email = ? LIMIT 1",
                [$username, $email]
            );
            if ($existing) {
                $this->updateStats($step, 'skipped');
                continue;
            }

            // Şifre — WP phpass hash'i BCRYPT değil, sıfırlama gerek
            $needsReset = '__RESET__' . $password;

            if ($this->dryRun) {
                $this->updateStats($step, 'imported');
                continue;
            }

            try {
                $this->targetDb->query(
                    "INSERT INTO nuke_users (username, user_email, user_password, user_level, created_at)
                     VALUES (?, ?, ?, ?, ?)",
                    [$username, $email, $needsReset, $level, $regdate]
                );
                $this->updateStats($step, 'imported');
            } catch (\Throwable $e) {
                $this->updateStats($step, 'failed');
                $this->errors[] = "Kullanıcı '$username': " . $e->getMessage();
            }
        }

        $this->finishStep($step);
        $this->logFinish($step);
        return $this->stepReport($step);
    }

    // ═══════════════════════════════════════════════════
    // HABERLER (wp_posts → nuke_stories)
    // ═══════════════════════════════════════════════════
    public function importNews(): array
    {
        $step = 'news';
        $this->startStep($step);

        $postsTable = $this->prefix . 'posts';
        if (!$this->tableExists($postsTable)) {
            $this->warnings[] = "Tablo yok: $postsTable";
            return $this->stepReport($step);
        }

        // Yazar map: WP user ID → username
        $authorMap = $this->buildAuthorMap();

        // Sadece yayınlanmış 'post' tipindeki yazılar
        $sql = "SELECT ID, post_author, post_date, post_content, post_title,
                       post_excerpt, post_name
                FROM `$postsTable`
                WHERE post_type = 'post' AND post_status = 'publish'
                ORDER BY ID ASC";

        $rows = $this->sourceDb->query($sql)->fetchAll();
        $this->stats[$step]['total'] = count($rows);
        $this->logStart($step, count($rows));

        // Term relationships hazır mı?
        $hasRelations = $this->tableExists($this->prefix . 'term_relationships')
                     && $this->tableExists($this->prefix . 'term_taxonomy');

        foreach ($rows as $row) {
            $title = trim($row['post_title'] ?? '');
            if ($title === '') {
                $this->updateStats($step, 'skipped');
                continue;
            }

            $slug = trim($row['post_name'] ?? '');
            if ($slug === '') {
                $slug = SlugHelper::slugify($title);
            } else {
                $slug = SlugHelper::slugify($slug);
            }
            if ($slug === 'n-a') $slug = 'haber';
            $baseSlug = $slug;
            $i = 2;
            while ($this->targetDb->fetch("SELECT sid FROM nuke_stories WHERE slug = ? LIMIT 1", [$slug])) {
                $slug = $baseSlug . '-' . $i++;
                if ($i > 1000) break;
            }

            // Yazar
            $authorName = $authorMap[(int)$row['post_author']] ?? 'admin';

            // Kategori — ilk kategoriyi al
            $topicId = 1; // Genel
            if ($hasRelations) {
                $topicId = $this->findPrimaryTopic((int)$row['ID']) ?: 1;
            }

            $hometext = trim($row['post_excerpt'] ?? '');
            $bodytext = (string)($row['post_content'] ?? '');
            $time = strtotime($row['post_date'] ?? 'now') ?: time();

            if ($this->dryRun) {
                $this->postMap[(int)$row['ID']] = (int)$row['ID'];
                $this->updateStats($step, 'imported');
                continue;
            }

            try {
                $this->targetDb->query(
                    "INSERT INTO nuke_stories
                     (title, slug, image, hometext, bodytext, aid, time, topic, informant)
                     VALUES (?, ?, '', ?, ?, ?, ?, ?, '')",
                    [$title, $slug, $hometext, $bodytext, $authorName, $time, $topicId]
                );
                $newSid = (int)$this->targetDb->getConnection()->lastInsertId();
                $this->postMap[(int)$row['ID']] = $newSid;
                $this->updateStats($step, 'imported');
            } catch (\Throwable $e) {
                $this->updateStats($step, 'failed');
                $this->errors[] = "Haber '$title': " . $e->getMessage();
            }
        }

        $this->finishStep($step);
        $this->logFinish($step);
        return $this->stepReport($step);
    }

    private function buildAuthorMap(): array
    {
        $map = [];
        $table = $this->prefix . 'users';
        if (!$this->tableExists($table)) return $map;

        $rows = $this->sourceDb->query("SELECT ID, user_login FROM `$table`")->fetchAll();
        foreach ($rows as $r) {
            $map[(int)$r['ID']] = $r['user_login'];
        }
        return $map;
    }

    private function findPrimaryTopic(int $postId): int
    {
        $relTable = $this->prefix . 'term_relationships';
        $taxTable = $this->prefix . 'term_taxonomy';

        $sql = "SELECT tt.term_id
                FROM `$relTable` tr
                INNER JOIN `$taxTable` tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
                WHERE tr.object_id = ? AND tt.taxonomy = 'category'
                LIMIT 1";
        $stmt = $this->sourceDb->prepare($sql);
        $stmt->execute([$postId]);
        $termId = $stmt->fetchColumn();

        if ($termId) {
            return $this->termMap[(int)$termId] ?? 1;
        }
        return 1;
    }

    // ═══════════════════════════════════════════════════
    // YORUMLAR
    // ═══════════════════════════════════════════════════
    public function importComments(): array
    {
        $step = 'comments';
        $this->startStep($step);

        $table = $this->prefix . 'comments';
        if (!$this->tableExists($table)) {
            $this->warnings[] = "Tablo yok: $table";
            return $this->stepReport($step);
        }

        $sql = "SELECT comment_ID, comment_post_ID, comment_author, comment_author_email,
                       comment_content, comment_date, comment_approved
                FROM `$table`
                WHERE comment_approved = 1
                ORDER BY comment_ID ASC";

        $rows = $this->sourceDb->query($sql)->fetchAll();
        $this->stats[$step]['total'] = count($rows);
        $this->logStart($step, count($rows));

        foreach ($rows as $row) {
            $wpPostId = (int)$row['comment_post_ID'];
            if (!isset($this->postMap[$wpPostId])) {
                $this->updateStats($step, 'skipped');
                continue;
            }

            $content = trim($row['comment_content'] ?? '');
            if ($content === '') {
                $this->updateStats($step, 'skipped');
                continue;
            }

            $newSid = $this->postMap[$wpPostId];
            $name = trim($row['comment_author'] ?? 'Anonim');
            $email = trim($row['comment_author_email'] ?? '');
            $date = strtotime($row['comment_date'] ?? 'now') ?: time();

            if ($this->dryRun) {
                $this->updateStats($step, 'imported');
                continue;
            }

            try {
                $this->targetDb->query(
                    "INSERT INTO nuke_comments
                     (sid, pid, author_name, author_email, author_id, author_ip, content, active, created_at)
                     VALUES (?, 0, ?, ?, 0, '', ?, 1, ?)",
                    [$newSid, $name, $email, $content, $date]
                );
                $this->updateStats($step, 'imported');
            } catch (\Throwable $e) {
                $this->updateStats($step, 'failed');
                $this->errors[] = "Yorum ($name): " . $e->getMessage();
            }
        }

        $this->finishStep($step);
        $this->logFinish($step);
        return $this->stepReport($step);
    }

    private function stepReport(string $step): array
    {
        return $this->stats[$step] ?? [];
    }
}
