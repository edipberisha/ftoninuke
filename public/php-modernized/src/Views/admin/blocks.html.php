<?php if (!empty($flash)): ?>
    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 text-green-400 rounded-lg">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<div class="bg-gray-900 border border-gray-800 rounded-xl p-6 mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-lg font-bold text-white">🧱 Bloklar (<?= count($blocks) ?>)</h2>
        <p class="text-xs text-gray-500 mt-1">Sol (L) / Sağ (R) / Üst (C) sütunlarda gösterilir</p>
    </div>
    <a href="/public/admin/blocks/create"
       class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-lg">
        + Yeni Blok
    </a>
</div>

<?php
$grouped = ['l' => [], 'c' => [], 'r' => []];
foreach ($blocks as $b) {
    $pos = $b['bposition'] ?? 'r';
    if (!isset($grouped[$pos])) $grouped[$pos] = [];
    $grouped[$pos][] = $b;
}
$posLabels = ['l' => '⬅️ Sol Sütun', 'c' => '⬆️ Üst Sütun', 'r' => '➡️ Sağ Sütun'];
?>

<?php foreach (['c', 'l', 'r'] as $pos): ?>
    <?php if (empty($grouped[$pos])) continue; ?>
    <div class="mb-8">
        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-3"><?= $posLabels[$pos] ?></h3>
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                    <tr>
                        <th class="text-left p-3">Sıra</th>
                        <th class="text-left p-3">Başlık</th>
                        <th class="text-left p-3">Tip</th>
                        <th class="text-left p-3">Önizleme</th>
                        <th class="text-center p-3">Durum</th>
                        <th class="text-right p-3">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($grouped[$pos] as $b): ?>
                    <tr class="border-t border-gray-800 hover:bg-gray-800/50">
                        <td class="p-3 text-gray-500 font-mono"><?= (int)$b['weight'] ?></td>
                        <td class="p-3 text-white font-bold"><?= htmlspecialchars($b['title']) ?></td>
                        <td class="p-3">
                            <?php
                            $typeBadges = [
                                'html'   => ['HTML',   'bg-blue-500/20 text-blue-400'],
                                'social' => ['Sosyal', 'bg-pink-500/20 text-pink-400'],
                                'ad'     => ['Reklam', 'bg-yellow-500/20 text-yellow-400'],
                                'php'    => ['PHP',    'bg-purple-500/20 text-purple-400'],
                            ];
                            $badge = $typeBadges[$b['block_type']] ?? ['?', 'bg-gray-700 text-gray-400'];
                            ?>
                            <span class="px-2 py-1 rounded text-xs <?= $badge[1] ?>"><?= $badge[0] ?></span>
                        </td>
                        <td class="p-3 text-gray-500 text-xs">
                            <?php
                            if ($b['block_type'] === 'html') {
                                echo htmlspecialchars(mb_substr(strip_tags($b['content'] ?? ''), 0, 60)) . '…';
                            } elseif ($b['block_type'] === 'ad') {
                                echo !empty($b['image']) ? '🖼️ görsel yüklü' : '<em>görsel yok</em>';
                            } elseif ($b['block_type'] === 'social') {
                                $cfg = json_decode($b['config'] ?? '{}', true) ?: [];
                                echo count(array_filter($cfg)) . ' sosyal link';
                            } elseif ($b['block_type'] === 'php') {
                                echo '📄 ' . htmlspecialchars($b['blockfile'] ?? '');
                            }
                            ?>
                        </td>
                        <td class="p-3 text-center">
                            <a href="/public/admin/blocks/toggle?bid=<?= (int)$b['bid'] ?>"
                               class="px-2 py-1 rounded text-xs <?= $b['active'] ? 'bg-green-500/20 text-green-400' : 'bg-gray-700 text-gray-400' ?>">
                                <?= $b['active'] ? '✓ Aktif' : '✗ Pasif' ?>
                            </a>
                        </td>
                        <td class="p-3 text-right space-x-2 whitespace-nowrap">
                            <a href="/public/admin/blocks/edit?bid=<?= (int)$b['bid'] ?>"
                               class="text-blue-400 hover:text-blue-300 text-xs">✏️ Düzenle</a>
                            <a href="/public/admin/blocks/delete?bid=<?= (int)$b['bid'] ?>"
                               onclick="return confirm('Bu bloğu silmek istediğinize emin misiniz?')"
                               class="text-red-400 hover:text-red-300 text-xs">🗑️ Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endforeach; ?>

<?php if (empty($blocks)): ?>
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-12 text-center text-gray-500 italic">
        Henüz blok yok. <a href="/public/admin/blocks/create" class="text-blue-400">İlk bloğu ekle</a>.
    </div>
<?php endif; ?>
