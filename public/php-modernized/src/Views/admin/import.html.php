<?php if (!empty($flash)): ?>
    <div class="mb-6 p-4 bg-blue-500/10 border border-blue-500/30 text-blue-400 rounded-lg">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<div class="mb-6">
    <div class="bg-gradient-to-r from-blue-900 to-purple-900 border border-blue-700/50 rounded-xl p-6">
        <h2 class="text-2xl font-black text-white mb-2">🔄 İçeri Aktar (Import)</h2>
        <p class="text-blue-200 text-sm">Eski PHP-Nuke sitenizin verilerini FtoniNuke'a taşıyın</p>
    </div>
</div>

<form method="post" action="/public/admin/import/run" class="space-y-6">

    <!-- KAYNAK SEÇİMİ -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <h3 class="text-white font-bold mb-4">📦 Kaynak Platform</h3>
        <div class="grid grid-cols-2 gap-4">
            <label class="cursor-pointer">
                <input type="radio" name="source" value="phpnuke" checked class="peer sr-only">
                <div class="border-2 border-gray-700 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 rounded-lg p-5 transition">
                    <div class="text-3xl mb-2">🗿</div>
                    <div class="text-white font-bold">PHP-Nuke</div>
                    <div class="text-xs text-gray-500 mt-1">Klasik PHP-Nuke 7.x / 8.x siteleri</div>
                </div>
            </label>
            <label class="cursor-pointer">
                <input type="radio" name="source" value="wordpress" class="peer sr-only">
                <div class="border-2 border-gray-700 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 rounded-lg p-5 transition">
                    <div class="text-3xl mb-2">📝</div>
                    <div class="text-white font-bold">WordPress</div>
                    <div class="text-xs text-gray-500 mt-1">WordPress 5.x / 6.x siteleri</div>
                </div>
            </label>
        </div>
    </div>

    <!-- BAĞLANTI -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <h3 class="text-white font-bold mb-4">🔌 Kaynak Veritabanı (Eski PHP-Nuke)</h3>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Sunucu (Host)</label>
                <input type="text" name="host" required placeholder="localhost veya IP"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Port</label>
                <input type="number" name="port" value="3306"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Veritabanı Adı</label>
                <input type="text" name="dbname" required placeholder="nuke_db"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Tablo Prefix</label>
                <input type="text" name="prefix" value="nuke_" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white font-mono focus:border-blue-500 focus:outline-none">
                <p class="text-xs text-gray-500 mt-1">PHP-Nuke: <code>nuke_</code> · WordPress: <code>wp_</code></p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Kullanıcı</label>
                <input type="text" name="user" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Şifre</label>
                <input type="password" name="pass"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
            </div>
        </div>
    </div>

    <!-- ADIMLAR -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <h3 class="text-white font-bold mb-4">📋 Aktarılacak Veriler</h3>

        <div class="space-y-3">
            <label class="flex items-center gap-3 bg-gray-800 rounded-lg p-4 cursor-pointer hover:bg-gray-750">
                <input type="checkbox" name="steps[]" value="categories" checked
                       class="w-5 h-5 rounded bg-gray-700 border-gray-600">
                <span class="text-white font-semibold">📁 Kategoriler</span>
                <span class="text-xs text-gray-500 ml-auto">nuke_topics → nuke_topics</span>
            </label>

            <label class="flex items-center gap-3 bg-gray-800 rounded-lg p-4 cursor-pointer hover:bg-gray-750">
                <input type="checkbox" name="steps[]" value="users"
                       class="w-5 h-5 rounded bg-gray-700 border-gray-600">
                <span class="text-white font-semibold">👥 Kullanıcılar</span>
                <span class="text-xs text-gray-500 ml-auto">nuke_users → nuke_users (⚠️ şifre sıfırlama gerekir)</span>
            </label>

            <label class="flex items-center gap-3 bg-gray-800 rounded-lg p-4 cursor-pointer hover:bg-gray-750">
                <input type="checkbox" name="steps[]" value="news" checked
                       class="w-5 h-5 rounded bg-gray-700 border-gray-600">
                <span class="text-white font-semibold">📰 Haberler</span>
                <span class="text-xs text-gray-500 ml-auto">nuke_stories → nuke_stories</span>
            </label>

            <label class="flex items-center gap-3 bg-gray-800 rounded-lg p-4 cursor-pointer hover:bg-gray-750">
                <input type="checkbox" name="steps[]" value="comments"
                       class="w-5 h-5 rounded bg-gray-700 border-gray-600">
                <span class="text-white font-semibold">💬 Yorumlar</span>
                <span class="text-xs text-gray-500 ml-auto">nuke_comments → nuke_comments</span>
            </label>
        </div>
    </div>

    <!-- MOD -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <h3 class="text-white font-bold mb-4">⚙️ Mod</h3>

        <label class="flex items-center gap-3 bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4 cursor-pointer">
            <input type="checkbox" name="dry_run" value="1" checked
                   class="w-5 h-5 rounded bg-gray-700 border-gray-600">
            <div>
                <div class="text-yellow-400 font-semibold">🔍 Test Modu (Dry-run)</div>
                <div class="text-xs text-gray-400 mt-1">Hiçbir veri yazılmaz, sadece ne olacağını gösterir</div>
            </div>
        </label>

        <p class="text-xs text-yellow-200 mt-3 bg-yellow-500/5 rounded p-3">
            ⚠️ <strong>Önemli:</strong> İlk denemede <strong>mutlaka Test Modu</strong> kullanın.
            Sonuçları inceledikten sonra gerçek aktarımı başlatın.
        </p>
    </div>

    <!-- BUTONLAR -->
    <div class="flex justify-end gap-3">
        <a href="/public/admin" class="px-6 py-3 text-gray-400 hover:text-white">
            İptal
        </a>
        <button type="submit"
                class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold px-8 py-3 rounded-lg">
            🚀 İçeri Aktarmayı Başlat
        </button>
    </div>
</form>

<!-- GEÇMİŞ -->
<?php if (!empty($logs)): ?>
    <div class="mt-8">
        <h3 class="text-white font-bold mb-4">📜 Geçmiş Aktarımlar</h3>
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                    <tr>
                        <th class="text-left p-3">Tarih</th>
                        <th class="text-left p-3">Kaynak</th>
                        <th class="text-left p-3">Adım</th>
                        <th class="text-center p-3">Toplam</th>
                        <th class="text-center p-3">Başarılı</th>
                        <th class="text-center p-3">Atlanan</th>
                        <th class="text-center p-3">Hatalı</th>
                        <th class="text-center p-3">Mod</th>
                        <th class="text-center p-3">Durum</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr class="border-t border-gray-800">
                        <td class="p-3 text-gray-500 text-xs"><?= date('d.m.Y H:i', (int)$log['started_at']) ?></td>
                        <td class="p-3 text-gray-400"><?= htmlspecialchars($log['source']) ?></td>
                        <td class="p-3 text-white"><?= htmlspecialchars($log['step']) ?></td>
                        <td class="p-3 text-center text-gray-400"><?= (int)$log['total'] ?></td>
                        <td class="p-3 text-center text-green-400"><?= (int)$log['imported'] ?></td>
                        <td class="p-3 text-center text-gray-500"><?= (int)$log['skipped'] ?></td>
                        <td class="p-3 text-center text-red-400"><?= (int)$log['failed'] ?></td>
                        <td class="p-3 text-center">
                            <?= $log['dry_run'] ? '<span class="text-yellow-400">Test</span>' : '<span class="text-blue-400">Gerçek</span>' ?>
                        </td>
                        <td class="p-3 text-center text-xs">
                            <span class="px-2 py-1 rounded <?= $log['status'] === 'done' ? 'bg-green-500/20 text-green-400' : 'bg-gray-700 text-gray-400' ?>">
                                <?= htmlspecialchars($log['status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
