<?php

declare(strict_types=1);

namespace FtoniNuke\Service\Import;

use FtoniNuke\Helper\SlugHelper;

/**
 * PHP-Nuke Importer
 *
 * Eski PHP-Nuke veritabanından FtoniNuke'a veri aktarır.
 *
 * Beklenen tablo yapısı (klasik PHP-Nuke):
 *   - nuke_stories (sid, aid, title, time, hometext, bodytext, topic, informant)
 *   - nuke_users (user_id, username, user_email, user_password, user_level, user_regdate)
 *   - nuke_topics (topicid, topicname, topicimage, topictext)
 *   - nuke_comments (tid, pid, sid, date, name, email, subject, comment, score)
 */
class PHPNukeImporter extends ImportService
{
    protected string $sourceName = 'phpnuke';
    protected string $prefix = 'nuke_';

    /** @var array Eski sid → Yeni sid eşlemesi (importNews sırasında doldurulur) */
    protected array $storyMap = [];

    public function __construct(string $prefix = 'nuke_')
    {
        parent::__construct();
        $this->prefix = $prefix;
    }

    /**
     * Prefix değiştir
     */
    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * Ön kontrol — kaynak tabloları doğrula
     */
    public function preflight(): array
    {
        $required = ['stories', 'users'];
        $optional = ['topics', 'comments'];

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

        $table = $this->prefix . 'topics';
        if (!$this->tableExists($table)) {
            $this->warnings[] = "Tablo yok: $table";
            return $this->stepReport($step);
        }

        $rows = $this->sourceDb->query("SELECT * FROM `$table`")->fetchAll();
        $this->stats[$step]['total'] = count($rows);
        $this->logStart($step, count($rows));

        foreach ($rows as $row) {
            $name = trim($row['topicname'] ?? $row['topic_name'] ?? '');
            if ($name === '') {
                $this->updateStats($step, 'skipped');
                continue;
            }

            // Aynı isim zaten var mı?
            $existing = $this->targetDb->fetch(
                "SELECT topic_id FROM nuke_topics WHERE topic_name = ? LIMIT 1",
                [$name]
            );
            if ($existing) {
                $this->updateStats($step, 'skipped');
                continue;
            }

            $slug = SlugHelper::slugify($name);
            if ($slug === 'n-a') $slug = 'topic';

            // Slug çakışması?
            $i = 2;
            $baseSlug = $slug;
            while ($this->targetDb->fetch("SELECT topic_id FROM nuke_topics WHERE topic_slug = ? LIMIT 1", [$slug])) {
                $slug = $baseSlug . '-' . $i++;
                if ($i > 100) break;
            }

            if ($this->dryRun) {
                $this->updateStats($step, 'imported');
                continue;
            }

            try {
                $this->targetDb->query(
                    "INSERT INTO nuke_topics (topic_name, topic_slug, topic_description, topic_image, created_at)
                     VALUES (?, ?, ?, ?, ?)",
                    [
                        $name,
                        $slug,
                        trim($row['topictext'] ?? ''),
                        trim($row['topicimage'] ?? ''),
                        time(),
                    ]
                );
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

        $rows = $this->sourceDb->query("SELECT * FROM `$table`")->fetchAll();
        $this->stats[$step]['total'] = count($rows);
        $this->logStart($step, count($rows));

        foreach ($rows as $row) {
            $username = trim($row['username'] ?? '');
            $email    = trim($row['user_email'] ?? '');
            $password = (string)($row['user_password'] ?? '');
            $level    = (int)($row['user_level'] ?? 1);
            $regdate  = (int)($row['user_regdate'] ?? time());

            if ($username === '') {
                $this->updateStats($step, 'skipped');
                continue;
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

            // Rol haritalama
            // PHP-Nuke: 1=user, 2=admin, 3=?, 9=superadmin
            // FtoniNuke: 0=üye, 1=editör, 2=admin
            if ($level >= 2) $level = 2;
            elseif ($level === 1) $level = 0;
            else $level = 0;

            // Şifre dönüşümü
            // PHP-Nuke şifreleri: MD5 veya düz metin (eski sürümler)
            // Modern sistem BCRYPT istiyor
            // → Kullanıcı ilk girişte şifre sıfırlasın diye özel bir hash koyuyoruz
            $needsReset = $this->convertPassword($password);

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

    /**
     * Şifreyi dönüştür — eski hash'i koruyamayız
     * Özel bir işaret koyuyoruz: reset gerektiren hash
     * Kullanıcı ilk girişte "şifremi unuttum" ile sıfırlayacak
     */
    private function convertPassword(string $oldHash): string
    {
        // FtoniNuke formatına özel prefix
        // Login kontrolü sırasında bu prefix varsa → "şifre sıfırlama gerekli" mesajı
        return '__RESET__' . $oldHash;
    }

    // ═══════════════════════════════════════════════════
    // HABERLER
    // ═══════════════════════════════════════════════════
    public function importNews(): array
    {
        $step = 'news';
        $this->startStep($step);

        $table = $this->prefix . 'stories';
        if (!$this->tableExists($table)) {
            $this->warnings[] = "Tablo yok: $table";
            return $this->stepReport($step);
        }

        // Topic map: eski topicid → yeni topic_id
        $topicMap = $this->buildTopicMap();

        $rows = $this->sourceDb->query("SELECT * FROM `$table` ORDER BY sid ASC")->fetchAll();
        $this->stats[$step]['total'] = count($rows);
        $this->logStart($step, count($rows));

        foreach ($rows as $row) {
            $title = trim($row['title'] ?? '');
            if ($title === '') {
                $this->updateStats($step, 'skipped');
                continue;
            }

            // Slug üret
            $slug = SlugHelper::slugify($title);
            if ($slug === 'n-a') $slug = 'haber';
            $baseSlug = $slug;
            $i = 2;
            while ($this->targetDb->fetch("SELECT sid FROM nuke_stories WHERE slug = ? LIMIT 1", [$slug])) {
                $slug = $baseSlug . '-' . $i++;
                if ($i > 1000) break;
            }

            // Topic eşleştirme
            $oldTopicId = (int)($row['topic'] ?? 0);
            $newTopicId = $topicMap[$oldTopicId] ?? 1; // default: Genel

            // Yazar eşleştirme (aid → username)
            $authorUsername = trim($row['aid'] ?? 'admin');

            // Dry-run: sadece say, storyMap'e eski sid'i koy
            if ($this->dryRun) {
                $this->storyMap[(int)$row['sid']] = (int)$row['sid'];
                $this->updateStats($step, 'imported');
                continue;
            }

            // Gerçek aktarım
            try {
                $this->targetDb->query(
                    "INSERT INTO nuke_stories
                     (title, slug, image, hometext, bodytext, aid, time, topic, informant)
                     VALUES (?, ?, '', ?, ?, ?, ?, ?, ?)",
                    [
                        $title,
                        $slug,
                        $row['hometext'] ?? '',
                        $row['bodytext'] ?? '',
                        $authorUsername,
                        (int)($row['time'] ?? time()),
                        $newTopicId,
                        trim($row['informant'] ?? ''),
                    ]
                );
                $newSid = (int)$this->targetDb->getConnection()->lastInsertId();
                $this->storyMap[(int)$row['sid']] = $newSid;
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

    /**
     * Eski topic id → yeni topic id eşleme
     */
    private function buildTopicMap(): array
    {
        $map = [];

        $table = $this->prefix . 'topics';
        if (!$this->tableExists($table)) return $map;

        $oldTopics = $this->sourceDb->query("SELECT * FROM `$table`")->fetchAll();

        foreach ($oldTopics as $old) {
            $name = trim($old['topicname'] ?? $old['topic_name'] ?? '');
            if ($name === '') continue;

            $new = $this->targetDb->fetch(
                "SELECT topic_id FROM nuke_topics WHERE topic_name = ? LIMIT 1",
                [$name]
            );

            if ($new) {
                $oldId = (int)($old['topicid'] ?? $old['topic_id'] ?? 0);
                $map[$oldId] = (int)$new['topic_id'];
            }
        }

        return $map;
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

        // Story map: importNews() sırasında dolduruldu mu?
        // Dolmadıysa (örn. sadece comments seçildiyse) fallback yap
        $storyMap = !empty($this->storyMap) ? $this->storyMap : $this->buildStoryMap();

        $rows = $this->sourceDb->query("SELECT * FROM `$table` ORDER BY tid ASC")->fetchAll();
        $this->stats[$step]['total'] = count($rows);
        $this->logStart($step, count($rows));

        foreach ($rows as $row) {
            $oldSid = (int)($row['sid'] ?? 0);
            if (!isset($storyMap[$oldSid])) {
                $this->updateStats($step, 'skipped');
                continue;
            }

            $newSid = $storyMap[$oldSid];
            $content = trim($row['comment'] ?? '');
            if ($content === '') {
                $this->updateStats($step, 'skipped');
                continue;
            }

            $name = trim($row['name'] ?? 'Anonim');
            $email = trim($row['email'] ?? '');
            $date = (int)($row['date'] ?? time());

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

    /**
     * Eski sid → yeni sid eşleme (title + time üzerinden)
     */
    private function buildStoryMap(): array
    {
        $map = [];
        $table = $this->prefix . 'stories';
        if (!$this->tableExists($table)) return $map;

        $oldStories = $this->sourceDb->query("SELECT sid, title, time FROM `$table`")->fetchAll();

        foreach ($oldStories as $old) {
            $title = trim($old['title'] ?? '');
            if ($title === '') continue;

            $new = $this->targetDb->fetch(
                "SELECT sid FROM nuke_stories WHERE title = ? AND time = ? LIMIT 1",
                [$title, (int)($old['time'] ?? 0)]
            );

            if ($new) {
                $map[(int)$old['sid']] = (int)$new['sid'];
            }
        }

        return $map;
    }

    // ═══════════════════════════════════════════════════
    // Yardımcılar
    // ═══════════════════════════════════════════════════
    private function stepReport(string $step): array
    {
        return $this->stats[$step] ?? [];
    }
}
