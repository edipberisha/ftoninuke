<?php
$db = \FtoniNuke\Core\Database::getInstance();

$users   = (int)($db->fetch("SELECT COUNT(*) AS c FROM nuke_users")['c'] ?? 0);
$stories = (int)($db->fetch("SELECT COUNT(*) AS c FROM nuke_stories")['c'] ?? 0);
$topics  = (int)($db->fetch("SELECT COUNT(*) AS c FROM nuke_topics")['c'] ?? 0);
$media   = (int)($db->fetch("SELECT COUNT(*) AS c FROM nuke_media")['c'] ?? 0);
?>
<ul class="block-stats">
    <li><span class="label">📝 Haberler</span><span class="value"><?= $stories ?></span></li>
    <li><span class="label">👥 Kullanıcılar</span><span class="value"><?= $users ?></span></li>
    <li><span class="label">📁 Kategoriler</span><span class="value"><?= $topics ?></span></li>
    <li><span class="label">🖼️ Medya</span><span class="value"><?= $media ?></span></li>
</ul>
