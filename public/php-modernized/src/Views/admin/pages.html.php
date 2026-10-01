<?php if (!empty($flash)): ?>
    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 text-green-400 rounded-lg">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<div class="bg-gray-900 border border-gray-800 rounded-xl p-6 mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-lg font-bold text-white">📄 Sayfalar (<?= count($pages) ?>)</h2>
        <p class="text-xs text-gray-500 mt-1">Hakkımızda, Gizlilik, İletişim gibi statik sayfalar</p>
    </div>
    <a href="/public/admin/pages/create"
       class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-lg">
        + Yeni Sayfa
    </a>
</div>

<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
            <tr>
                <th class="text-left p-3">Sıra</th>
                <th class="text-left p-3">Başlık</th>
                <th class="text-left p-3">Slug</th>
                <th class="text-left p-3">Şablon</th>
                <th class="text-center p-3">Menüde</th>
                <th class="text-center p-3">Durum</th>
                <th class="text-right p-3">İşlem</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($pages)): ?>
            <tr><td colspan="7" class="text-center p-8 text-gray-600 italic">Sayfa yok.</td></tr>
        <?php else: foreach ($pages as $p): ?>
            <tr class="border-t border-gray-800 hover:bg-gray-800/50">
                <td class="p-3 text-gray-500 font-mono"><?= (int)$p['sort_order'] ?></td>
                <td class="p-3 text-white font-bold"><?= htmlspecialchars($p['title']) ?></td>
                <td class="p-3 text-gray-400 font-mono text-xs">/public/<?= htmlspecialchars($p['slug']) ?></td>
                <td class="p-3">
                    <?php
                    $tpl = $p['template'] ?? 'default';
                    $tplLabels = [
                        'default' => ['Default', 'bg-gray-700 text-gray-300'],
                        'contact' => ['İletişim', 'bg-blue-500/20 text-blue-400'],
                        'full'    => ['Tam Genişlik', 'bg-purple-500/20 text-purple-400'],
                    ];
                    $label = $tplLabels[$tpl] ?? [$tpl, 'bg-gray-700 text-gray-400'];
                    ?>
                    <span class="px-2 py-1 rounded text-xs <?= $label[1] ?>"><?= $label[0] ?></span>
                </td>
                <td class="p-3 text-center">
                    <?= $p['show_in_menu'] ? '✅' : '—' ?>
                </td>
                <td class="p-3 text-center">
                    <a href="/public/admin/pages/toggle?id=<?= (int)$p['id'] ?>"
                       class="px-2 py-1 rounded text-xs <?= $p['active'] ? 'bg-green-500/20 text-green-400' : 'bg-gray-700 text-gray-400' ?>">
                        <?= $p['active'] ? '✓ Aktif' : '✗ Pasif' ?>
                    </a>
                </td>
                <td class="p-3 text-right space-x-2 whitespace-nowrap">
                    <a href="/public/<?= htmlspecialchars($p['slug']) ?>" target="_blank"
                       class="text-gray-400 hover:text-white text-xs">👁️</a>
                    <a href="/public/admin/pages/edit?id=<?= (int)$p['id'] ?>"
                       class="text-blue-400 hover:text-blue-300 text-xs">✏️</a>
                    <a href="/public/admin/pages/delete?id=<?= (int)$p['id'] ?>"
                       onclick="return confirm('Bu sayfayı silmek istediğinize emin misiniz?')"
                       class="text-red-400 hover:text-red-300 text-xs">🗑️</a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
