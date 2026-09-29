<?php if (!empty($flash)): ?>
    <div class="mb-6 p-4 bg-blue-500/10 border border-blue-500/30 text-blue-400 rounded-lg">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<!-- Filtreler -->
<div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mb-6 flex justify-between items-center flex-wrap gap-3">
    <div class="flex gap-2">
        <a href="/public/admin/comments?filter=pending"
           class="px-4 py-2 rounded-lg text-sm font-bold transition <?= ($filter ?? '') === 'pending' ? 'bg-yellow-500 text-black' : 'bg-gray-800 text-gray-400 hover:bg-gray-700' ?>">
            ⏳ Bekleyen <?= $pending > 0 ? '(' . $pending . ')' : '' ?>
        </a>
        <a href="/public/admin/comments?filter=approved"
           class="px-4 py-2 rounded-lg text-sm font-bold transition <?= ($filter ?? '') === 'approved' ? 'bg-green-500 text-black' : 'bg-gray-800 text-gray-400 hover:bg-gray-700' ?>">
            ✅ Onaylı
        </a>
        <a href="/public/admin/comments?filter=all"
           class="px-4 py-2 rounded-lg text-sm font-bold transition <?= ($filter ?? '') === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700' ?>">
            📋 Tümü
        </a>
    </div>
    <div class="text-xs text-gray-500">
        Toplam: <strong class="text-white"><?= count($comments) ?></strong> yorum
    </div>
</div>

<!-- Yorum Listesi -->
<?php if (empty($comments)): ?>
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-12 text-center text-gray-500 italic">
        Bu kategoride yorum yok.
    </div>
<?php else: ?>
    <div class="space-y-3">
        <?php foreach ($comments as $c): ?>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 hover:border-gray-700 transition">
                <div class="flex justify-between items-start gap-4 mb-3 flex-wrap">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 flex-wrap mb-1">
                            <strong class="text-white"><?= htmlspecialchars($c['author_name']) ?></strong>
                            <?php if ($c['author_id'] > 0): ?>
                                <span class="px-2 py-0.5 bg-blue-500/20 text-blue-400 text-xs rounded">Üye</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 bg-gray-700 text-gray-400 text-xs rounded">Misafir</span>
                            <?php endif; ?>
                            <span class="text-xs text-gray-500"><?= date('d.m.Y H:i', (int)$c['created_at']) ?></span>
                            <span class="text-xs text-gray-500 font-mono"><?= htmlspecialchars($c['author_ip']) ?></span>
                        </div>
                        <?php if (!empty($c['story_title'])): ?>
                            <div class="text-xs text-gray-500">
                                📄 Haber: <a href="/public/?sid=<?= (int)$c['sid'] ?>" target="_blank" class="text-blue-400 hover:underline">
                                    <?= htmlspecialchars($c['story_title']) ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if ($c['active']): ?>
                            <span class="px-3 py-1 bg-green-500/20 text-green-400 text-xs font-bold rounded">✓ Onaylı</span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-yellow-500/20 text-yellow-400 text-xs font-bold rounded">⏳ Bekliyor</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-gray-300 text-sm leading-relaxed bg-gray-800/50 rounded-lg p-3 mb-3 whitespace-pre-wrap"><?= htmlspecialchars($c['content']) ?></div>

                <div class="flex gap-2 flex-wrap">
                    <?php if (!$c['active']): ?>
                        <a href="/public/admin/comments?filter=<?= htmlspecialchars($filter) ?>&approve=<?= (int)$c['cid'] ?>"
                           class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded">✅ Onayla</a>
                    <?php else: ?>
                        <a href="/public/admin/comments?filter=<?= htmlspecialchars($filter) ?>&unapprove=<?= (int)$c['cid'] ?>"
                           class="px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-bold rounded">⏳ Onaydan Kaldır</a>
                    <?php endif; ?>
                    <a href="/public/admin/comments?filter=<?= htmlspecialchars($filter) ?>&ban=<?= (int)$c['cid'] ?>"
                       onclick="return confirm('Bu IP adresini yasaklamak istediğinize emin misiniz?')"
                       class="px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded">🚫 IP Ban</a>
                    <a href="/public/admin/comments?filter=<?= htmlspecialchars($filter) ?>&delete=<?= (int)$c['cid'] ?>"
                       onclick="return confirm('Bu yorumu silmek istediğinize emin misiniz? Alt yanıtlar da silinir.')"
                       class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded">🗑️ Sil</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Ban Listesi -->
<?php if (!empty($bannedIps)): ?>
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 mt-8">
        <h3 class="text-white font-bold mb-4">🚫 Yasaklı IP Adresleri (<?= count($bannedIps) ?>)</h3>
        <div class="space-y-2">
            <?php foreach ($bannedIps as $b): ?>
                <div class="flex justify-between items-center bg-gray-800 rounded-lg px-4 py-2 text-sm">
                    <div class="flex gap-4 items-center">
                        <code class="text-orange-400 font-mono"><?= htmlspecialchars($b['ip_address']) ?></code>
                        <span class="text-gray-500 text-xs"><?= htmlspecialchars($b['reason']) ?></span>
                        <span class="text-gray-600 text-xs"><?= date('d.m.Y', (int)$b['created_at']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
