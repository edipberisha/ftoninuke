<?php
/**
 * Dinamik sitemap.xml
 * Erişim: /public/sitemap.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use FtoniNuke\Core\Database;

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
         . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');

$db = Database::getInstance();

try {
    $config   = $db->fetch("SELECT sitename FROM nuke_config LIMIT 1");
    $siteName = $config['sitename'] ?? 'FtoniNuke';
} catch (\Throwable $e) {
    $siteName = 'FtoniNuke';
}

$stories = [];
try {
    $stories = $db->fetchAll(
        "SELECT slug, sid, time FROM nuke_stories ORDER BY time DESC LIMIT 1000"
    );
} catch (\Throwable $e) {}

$topics = [];
try {
    $topics = $db->fetchAll("SELECT topic_slug, topic_name FROM nuke_topics ORDER BY topic_name");
} catch (\Throwable $e) {}

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc><?= htmlspecialchars($baseUrl) ?>/public/</loc>
        <changefreq>hourly</changefreq>
        <priority>1.0</priority>
        <lastmod><?= date('c') ?></lastmod>
    </url>

    <?php foreach ($stories as $s): ?>
        <?php
        $url = !empty($s['slug'])
            ? $baseUrl . '/public/' . htmlspecialchars($s['slug'])
            : $baseUrl . '/public/?sid=' . (int)$s['sid'];
        ?>
        <url>
            <loc><?= $url ?></loc>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
            <?php if (!empty($s['time'])): ?>
                <lastmod><?= date('c', (int)$s['time']) ?></lastmod>
            <?php endif; ?>
        </url>
    <?php endforeach; ?>

    <?php foreach ($topics as $t): ?>
        <url>
            <loc><?= htmlspecialchars($baseUrl) ?>/public/kategori/<?= htmlspecialchars($t['topic_slug']) ?></loc>
            <changefreq>daily</changefreq>
            <priority>0.6</priority>
        </url>
    <?php endforeach; ?>

</urlset>
