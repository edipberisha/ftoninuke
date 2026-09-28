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

        public function show(int $sid): void
    {
        $repo  = new NewsRepository();
        $story = $repo->findById($sid);

        if (!$story) {
            http_response_code(404);
            echo '<h1>Haber bulunamadı</h1>';
            echo '<p><a href="/public/">Ana sayfaya dön</a></p>';
            return;
        }

        // Sonraki / Önceki haber
        $nextStory = $repo->findNext($sid, (int)$story['time']);
        $prevStory = $repo->findPrev($sid, (int)$story['time']);

        // Site adı
        try {
            $db = \FtoniNuke\Core\Database::getInstance();
            $config = $db->fetch("SELECT sitename FROM nuke_config LIMIT 1");
            $siteName = $config['sitename'] ?? 'FtoniNuke Portal';
        } catch (\Throwable $e) {
            $siteName = 'FtoniNuke Portal';
        }

        require __DIR__ . '/../Views/news-detail.html.php';
    }
}