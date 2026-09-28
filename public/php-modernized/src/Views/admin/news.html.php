<?php if (!empty($flash)): ?>
    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 text-green-400 rounded-lg">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<div class="bg-gray-900 border border-gray-800 rounded-xl p-6 mb-6 flex justify-between items-center">
    <h2 class="text-lg font-bold text-white">📰 Haberler (<?= count($newsList) ?>)</h2>
    <a href="/public/admin/news/create"
       class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition">
        + Yeni Haber
    </a>
</div>

<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
            <tr>
                <th class="text-left p-4">ID</th>
                <th class="text-left p-4">Başlık</th>
                <th class="text-left p-4">Yazar</th>
                <th class="text-left p-4">Tarih</th>
                <th class="text-right p-4">İşlem</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($newsList)): ?>
            <tr><td colspan="5" class="text-center p-8 text-gray-600 italic">Henüz haber yok.</td></tr>
        <?php else: foreach ($newsList as $n): ?>
            <tr class="border-t border-gray-800 hover:bg-gray-800/50">
                <td class="p-4 text-gray-500"><?= (int)$n['sid'] ?></td>
                <td class="p-4 text-white font-bold"><?= htmlspecialchars($n['title']) ?></td>
                <td class="p-4 text-gray-400"><?= htmlspecialchars($n['author_name'] ?? $n['aid'] ?? '-') ?></td>
                <td class="p-4 text-gray-500"><?= date('d.m.Y H:i', (int)$n['time']) ?></td>
                <td class="p-4 text-right space-x-2">
                    <a href="/public/?sid=<?= (int)$n['sid'] ?>" target="_blank"
                       class="text-gray-400 hover:text-white text-xs">👁️ Görüntüle</a>
                    <a href="/public/admin/news/edit?sid=<?= (int)$n['sid'] ?>"
                       class="text-blue-400 hover:text-blue-300 text-xs">✏️ Düzenle</a>
                    <a href="/public/admin/news/delete?sid=<?= (int)$n['sid'] ?>"
                       onclick="return confirm('Bu haberi silmek istediğinize emin misiniz?')"
                       class="text-red-400 hover:text-red-300 text-xs">🗑️ Sil</a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>