<?php if (!empty($flash)): ?>
    <div class="mb-6 p-4 bg-blue-500/10 border border-blue-500/30 text-blue-400 rounded-lg">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Sol: Menü Listesi -->
    <div class="lg:col-span-2 bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="p-6 border-b border-gray-800 flex justify-between items-center">
            <h2 class="text-lg font-bold text-white">🧭 Menü Öğeleri (<?= count($items) ?>)</h2>
            <a href="/public/" target="_blank" class="text-xs text-gray-500 hover:text-white">Siteyi görüntüle →</a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Sıra</th>
                    <th class="text-left p-3">Başlık</th>
                    <th class="text-left p-3">URL</th>
                    <th class="text-left p-3">Üst</th>
                    <th class="text-center p-3">Durum</th>
                    <th class="text-right p-3">İşlem</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="6" class="text-center p-8 text-gray-600 italic">Menü öğesi yok.</td></tr>
            <?php else: foreach ($items as $item): ?>
                <tr class="border-t border-gray-800 hover:bg-gray-800/50">
                    <td class="p-3 text-gray-500 font-mono"><?= (int)$item['sort_order'] ?></td>
                    <td class="p-3 text-white font-bold">
                        <?php if ((int)$item['parent_id'] > 0): ?>
                            <span class="text-gray-600">└─</span>
                        <?php endif; ?>
                        <?= htmlspecialchars($item['title']) ?>
                    </td>
                    <td class="p-3 text-gray-400 font-mono text-xs"><?= htmlspecialchars($item['url']) ?></td>
                    <td class="p-3 text-gray-500 text-xs">
                        <?php
                            $parent = $item['parent_id'] > 0
                                ? array_filter($items, fn($i) => (int)$i['id'] === (int)$item['parent_id'])
                                : [];
                            $pname = !empty($parent) ? reset($parent)['title'] : '—';
                            echo htmlspecialchars($pname);
                        ?>
                    </td>
                    <td class="p-3 text-center">
                        <a href="/public/admin/menu?toggle=<?= (int)$item['id'] ?>"
                           class="px-2 py-1 rounded text-xs <?= $item['active'] ? 'bg-green-500/20 text-green-400' : 'bg-gray-700 text-gray-400' ?>">
                            <?= $item['active'] ? '✓ Aktif' : '✗ Pasif' ?>
                        </a>
                    </td>
                    <td class="p-3 text-right space-x-2 whitespace-nowrap">
                        <button type="button" onclick="editItem(<?= htmlspecialchars(json_encode([
                            'id'         => (int)$item['id'],
                            'title'      => $item['title'],
                            'url'        => $item['url'],
                            'target'     => $item['target'],
                            'parent_id'  => (int)$item['parent_id'],
                            'sort_order' => (int)$item['sort_order'],
                            'active'     => (int)$item['active'],
                        ]), ENT_QUOTES) ?>)"
                                class="text-blue-400 hover:text-blue-300 text-xs">✏️ Düzenle</button>
                        <a href="/public/admin/menu?delete=<?= (int)$item['id'] ?>"
                           onclick="return confirm('Bu menü öğesini silmek istediğinize emin misiniz? Alt menüler de silinir.')"
                           class="text-red-400 hover:text-red-300 text-xs">🗑️ Sil</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Sağ: Form -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <h3 class="text-white font-bold mb-4" id="form-title">+ Yeni Menü Öğesi</h3>
        <form method="post" id="menu-form" class="space-y-4">
            <input type="hidden" name="edit_id" id="edit_id" value="0">

            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Başlık *</label>
                <input type="text" name="title" id="f-title" required placeholder="Örn: Hakkımızda"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">URL *</label>
                <input type="text" name="url" id="f-url" required placeholder="/public/hakkimizda"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm font-mono focus:border-blue-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Hedef</label>
                    <select name="target" id="f-target"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:border-blue-500 focus:outline-none">
                        <option value="_self">Aynı sekme</option>
                        <option value="_blank">Yeni sekme</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Sıra</label>
                    <input type="number" name="sort_order" id="f-sort" value="0"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Üst Menü (opsiyonel)</label>
                <select name="parent_id" id="f-parent"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:border-blue-500 focus:outline-none">
                    <option value="0">— Ana menü —</option>
                    <?php foreach ($items as $it): ?>
                        <?php if ((int)$it['parent_id'] === 0): ?>
                            <option value="<?= (int)$it['id'] ?>"><?= htmlspecialchars($it['title']) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-300">
                <input type="checkbox" name="active" id="f-active" checked
                       class="rounded bg-gray-800 border-gray-700">
                Aktif (menüde göster)
            </label>

            <div class="flex gap-2 pt-4 border-t border-gray-800">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2 rounded-lg">
                    💾 Kaydet
                </button>
                <button type="button" onclick="resetForm()"
                        class="px-4 py-2 text-gray-400 hover:text-white text-sm">Sıfırla</button>
            </div>
        </form>
    </div>
</div>

<script>
function editItem(data) {
    document.getElementById('form-title').textContent = '✏️ Menü Öğesini Düzenle';
    document.getElementById('edit_id').value = data.id;
    document.getElementById('f-title').value = data.title;
    document.getElementById('f-url').value = data.url;
    document.getElementById('f-target').value = data.target || '_self';
    document.getElementById('f-parent').value = data.parent_id || 0;
    document.getElementById('f-sort').value = data.sort_order || 0;
    document.getElementById('f-active').checked = data.active == 1;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('form-title').textContent = '+ Yeni Menü Öğesi';
    document.getElementById('edit_id').value = '0';
    document.getElementById('menu-form').reset();
    document.getElementById('f-active').checked = true;
}
</script>
