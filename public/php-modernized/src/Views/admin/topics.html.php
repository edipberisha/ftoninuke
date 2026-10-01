<?php if (!empty($flash)): ?>
    <div class="mb-6 p-4 bg-blue-500/10 border border-blue-500/30 text-blue-400 rounded-lg">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- SOL: Kategori Listesi -->
    <div class="lg:col-span-2">
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="p-6 border-b border-gray-800 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-bold text-white">📁 Kategoriler (<?= count($topics) ?>)</h2>
                    <p class="text-xs text-gray-500 mt-1">Haberleri gruplandırmak için kullanılır</p>
                </div>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                    <tr>
                        <th class="text-left p-4">#</th>
                        <th class="text-left p-4">Ad</th>
                        <th class="text-left p-4">Slug</th>
                        <th class="text-center p-4">Haber</th>
                        <th class="text-right p-4">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($topics)): ?>
                    <tr><td colspan="5" class="text-center p-8 text-gray-600 italic">Kategori yok.</td></tr>
                <?php else: foreach ($topics as $t): ?>
                    <tr class="border-t border-gray-800 hover:bg-gray-800/50">
                        <td class="p-4 text-gray-500 font-mono"><?= (int)$t['topic_id'] ?></td>
                        <td class="p-4 text-white font-bold">
                            <?= htmlspecialchars($t['topic_name']) ?>
                            <?php if (!empty($t['topic_description'])): ?>
                                <div class="text-xs text-gray-500 font-normal mt-1">
                                    <?= htmlspecialchars(mb_substr($t['topic_description'], 0, 80)) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-gray-400 font-mono text-xs"><?= htmlspecialchars($t['topic_slug']) ?></td>
                        <td class="p-4 text-center">
                            <?php $cnt = $topicCounts[(int)$t['topic_id']] ?? 0; ?>
                            <?php if ($cnt > 0): ?>
                                <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-bold">
                                    <?= $cnt ?>
                                </span>
                            <?php else: ?>
                                <span class="text-gray-600">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-right space-x-2 whitespace-nowrap">
                            <button type="button" onclick="editTopic(<?= htmlspecialchars(json_encode([
                                'id'   => (int)$t['topic_id'],
                                'name' => $t['topic_name'],
                                'desc' => $t['topic_description'] ?? '',
                            ]), ENT_QUOTES) ?>)"
                                    class="text-blue-400 hover:text-blue-300 text-xs">✏️ Düzenle</button>
                            <a href="/public/admin/topics?delete=<?= (int)$t['topic_id'] ?>"
                               onclick="return confirm('<?= htmlspecialchars($t['topic_name']) ?> kategorisini silmek istediğinize emin misiniz?')"
                               class="text-red-400 hover:text-red-300 text-xs">🗑️ Sil</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SAĞ: Form -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <h3 class="text-white font-bold mb-4" id="form-title">+ Yeni Kategori</h3>

        <form method="post" id="topic-form" class="space-y-4">
            <input type="hidden" name="edit_id" id="edit_id" value="0">

            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Kategori Adı *</label>
                <input type="text" name="topic_name" id="f-name" required
                       placeholder="örn: Teknoloji"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white text-sm focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Açıklama</label>
                <textarea name="topic_description" id="f-desc" rows="3"
                          placeholder="Kısa açıklama (opsiyonel)"
                          class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white text-sm focus:border-blue-500 focus:outline-none"></textarea>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-3 rounded-lg">
                💾 Kaydet
            </button>

            <button type="button" onclick="resetForm()"
                    class="w-full text-gray-400 hover:text-white text-xs py-2">
                Sıfırla
            </button>
        </form>
    </div>
</div>

<script>
function editTopic(data) {
    document.getElementById('form-title').textContent = '✏️ Kategori Düzenle';
    document.getElementById('edit_id').value = data.id;
    document.getElementById('f-name').value = data.name;
    document.getElementById('f-desc').value = data.desc;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('form-title').textContent = '+ Yeni Kategori';
    document.getElementById('edit_id').value = '0';
    document.getElementById('topic-form').reset();
}
</script>
