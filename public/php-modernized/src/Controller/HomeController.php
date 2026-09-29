<?php

declare(strict_types=1);

namespace FtoniNuke\Controller;

use FtoniNuke\Core\Database;
use FtoniNuke\Repository\NewsRepository;

class HomeController
{
    public function index(): void
    {
        $db   = Database::getInstance();
        $repo = new NewsRepository();

        // Site adı
        try {
            $config   = $db->fetch("SELECT sitename FROM nuke_config LIMIT 1");
            $siteName = $config['sitename'] ?? 'FtoniNuke Portal';
        } catch (\Throwable $e) {
            $siteName = 'FtoniNuke Portal';
        }

        // Haberler
        $news = $repo->all(10, 0);

        require __DIR__ . '/../Views/home.html.php';
    }

    /**
     * Eski yöntem: ?sid=X
     * Eğer slug varsa SEO URL'ye 301 yönlendirir.
     */
    public function show(int $sid): void
    {
        $repo  = new NewsRepository();
        $story = $repo->findById($sid);

        if (!$story) {
            $this->notFound();
            return;
        }

        // Slug varsa SEO URL'ye 301 yönlendir
        if (!empty($story['slug'])) {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: /public/' . $story['slug']);
            exit;
        }

        $this->renderStory($story);
    }

    /**
     * Yeni yöntem: /public/{slug}
     */
    public function showBySlug(string $slug): void
    {
        // .html uzantısını temizle
        $slug = preg_replace('/\.html?$/i', '', $slug);

        $repo  = new NewsRepository();
        $story = $repo->findBySlug($slug);

        if (!$story) {
            $this->notFound();
            return;
        }

        $this->renderStory($story);
    }

    /**
     * Ortak render
     */
    private function renderStory(array $story): void
    {
        $repo = new NewsRepository();
        $sid  = (int)$story['sid'];

        // Sonraki / Önceki
        $nextStory = $repo->findNext($sid, (int)$story['time']);
        $prevStory = $repo->findPrev($sid, (int)$story['time']);

        // Site adı
        try {
            $db = Database::getInstance();
            $config = $db->fetch("SELECT sitename FROM nuke_config LIMIT 1");
            $siteName = $config['sitename'] ?? 'FtoniNuke Portal';
        } catch (\Throwable $e) {
            $siteName = 'FtoniNuke Portal';
        }

        require __DIR__ . '/../Views/news-detail.html.php';
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Haber bulunamadı</title>';
        echo '<style>body{font-family:system-ui;text-align:center;padding:80px 20px;background:#f0f2f5}';
        echo 'h1{color:#2c3e50}a{color:#2980b9}</style></head><body>';
        echo '<h1>📰 404 — Haber bulunamadı</h1>';
        echo '<p><a href="/public/">← Ana sayfaya dön</a></p>';
        echo '</body></html>';
    }
}
