<?php
/**
 * Son Haberler Bloğu
 * $block = nuke_blocks tablosundan gelen veri
 */

$newsRepo = new \FtoniNuke\Repository\NewsRepository();
$news = $newsRepo->all(5, 0);
?>
<ul class="block-list">
    <?php foreach ($news as $item): ?>
        <li>
            <a href="/public/?sid=<?= (int)$item['sid'] ?>">
                <?= htmlspecialchars($item['title']) ?>
            </a>
            <small><?= date('d.m.Y', (int)$item['time']) ?></small>
        </li>
    <?php endforeach; ?>
    <?php if (empty($news)): ?>
        <li><em>Henüz haber yok.</em></li>
    <?php endif; ?>
</ul>
