<div class="mb-6">
    <div class="<?= $report['dry_run'] ? 'bg-gradient-to-r from-yellow-900 to-orange-900 border-yellow-700/50' : 'bg-gradient-to-r from-green-900 to-blue-900 border-green-700/50' ?> border rounded-xl p-6">
        <h2 class="text-2xl font-black text-white mb-2">
            <?= $report['dry_run'] ? '🔍 Test Sonucu (Hiçbir veri yazılmadı)' : '✅ Aktarım Tamamlandı' ?>
        </h2>
        <p class="text-sm <?= $report['dry_run'] ? 'text-yellow-200' : 'text-green-200' ?>">
            Aşağıdaki rapor, aktarım sonuçlarını gösterir.
        </p>
    </div>
</div>

<!-- PREFLIGHT -->
<div class="bg-gray-900 border border-gray-800 rounded-xl p-6 mb-6">
    <h3 class="text-white font-bold mb-4">📊 Kaynak Tablo Kontrolü</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <?php foreach ($report['preflight']['required'] as $table => $exists): ?>
            <div class="flex items-center gap-2 p-3 rounded-lg <?= $exists ? 'bg-green-500/10' : 'bg-red-500/10' ?>">
                <span class="text-xl"><?= $exists ? '✅' : '❌' ?></span>
                <code class="text-sm <?= $exists ? 'text-green-400' : 'text-red-400' ?>"><?= htmlspecialchars($table) ?></code>
            </div>
        <?php endforeach; ?>
        <?php foreach ($report['preflight']['optional'] as $table => $exists): ?>
            <div class="flex items-center gap-2 p-3 rounded-lg bg-gray-800/50">
                <span class="text-xl"><?= $exists ? '✅' : '⚪' ?></span>
                <code class="text-sm text-gray-500"><?= htmlspecialchars($table) ?></code>
                <span class="text-xs text-gray-600 ml-auto">opsiyonel</span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- STATS -->
<div class="bg-gray-900 border border-gray-800 rounded-xl p-6 mb-6">
    <h3 class="text-white font-bold mb-4">📈 İstatistikler</h3>

    <?php if (empty($report['stats'])): ?>
        <p class="text-gray-500 italic">Hiçbir adım çalıştırılmadı.</p>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($report['stats'] as $step => $stat): ?>
                <div class="bg-gray-800 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-white font-bold text-lg">
                            <?php
                            $icons = ['categories' => '📁', 'users' => '👥', 'news' => '📰', 'comments' => '💬'];
                            $labels = ['categories' => 'Kategoriler', 'users' => 'Kullanıcılar', 'news' => 'Haberler', 'comments' => 'Yorumlar'];
                            echo ($icons[$step] ?? '📦') . ' ' . ($labels[$step] ?? $step);
                            ?>
                        </h4>
                        <?php if (!empty($stat['finished'])): ?>
                            <span class="text-xs text-gray-500">
                                Süre: <?= (int)($stat['finished'] - $stat['started']) ?> sn
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-4 gap-3">
                        <div class="text-center bg-gray-900 rounded p-3">
                            <div class="text-2xl font-bold text-gray-400"><?= (int)$stat['total'] ?></div>
                            <div class="text-xs text-gray-500 mt-1">Toplam</div>
                        </div>
                        <div class="text-center bg-green-500/10 rounded p-3">
                            <div class="text-2xl font-bold text-green-400"><?= (int)$stat['imported'] ?></div>
                            <div class="text-xs text-green-600 mt-1">Aktarıldı</div>
                        </div>
                        <div class="text-center bg-gray-500/10 rounded p-3">
                            <div class="text-2xl font-bold text-gray-400"><?= (int)$stat['skipped'] ?></div>
                            <div class="text-xs text-gray-500 mt-1">Atlandı</div>
                        </div>
                        <div class="text-center bg-red-500/10 rounded p-3">
                            <div class="text-2xl font-bold text-red-400"><?= (int)$stat['failed'] ?></div>
                            <div class="text-xs text-red-600 mt-1">Hatalı</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- UYARILAR -->
<?php if (!empty($report['warnings'])): ?>
    <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-6 mb-6">
        <h3 class="text-yellow-400 font-bold mb-3">⚠️ Uyarılar</h3>
        <ul class="space-y-1 text-sm text-yellow-200">
            <?php foreach ($report['warnings'] as $w): ?>
                <li>• <?= htmlspecialchars($w) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- HATALAR -->
<?php if (!empty($report['errors'])): ?>
    <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-6 mb-6">
        <h3 class="text-red-400 font-bold mb-3">❌ Hatalar (<?= count($report['errors']) ?>)</h3>
        <div class="max-h-64 overflow-y-auto">
            <ul class="space-y-1 text-sm text-red-200 font-mono">
                <?php foreach (array_slice($report['errors'], 0, 50) as $e): ?>
                    <li>• <?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
                <?php if (count($report['errors']) > 50): ?>
                    <li class="text-gray-500 italic">... ve <?= count($report['errors']) - 50 ?> hata daha</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<!-- BUTONLAR -->
<div class="flex justify-between gap-3">
    <a href="/public/admin/import" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold">
        ← Yeni Aktarım
    </a>

    <?php if ($report['dry_run']): ?>
        <a href="/public/admin/import" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-bold">
            ⚡ Gerçek Aktarımı Başlat
        </a>
    <?php else: ?>
        <a href="/public/admin/news" class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold">
            📰 Haberleri Gör →
        </a>
    <?php endif; ?>
</div>
