<?php if (!empty($errors)): ?>
    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-lg">
        <ul class="list-disc list-inside">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
    .ql-toolbar.ql-snow { background: #1f2937; border-color: #374151 !important; border-radius: 8px 8px 0 0; }
    .ql-container.ql-snow { background: #1f2937; border-color: #374151 !important; border-radius: 0 0 8px 8px; color: #e5e7eb; font-size: 15px; min-height: 350px; }
    .ql-editor.ql-blank::before { color: #6b7280; font-style: normal; }
    .ql-snow .ql-stroke { stroke: #9ca3af; }
    .ql-snow .ql-fill { fill: #9ca3af; }
    .ql-snow .ql-picker { color: #9ca3af; }
    .ql-snow .ql-picker-options { background: #1f2937; border-color: #374151; }
    .ql-snow .ql-picker-item { color: #e5e7eb; }
</style>

<form method="post" id="pageForm" class="bg-gray-900 border border-gray-800 rounded-xl p-8 space-y-5">

    <div class="grid grid-cols-2 gap-5">
        <div>
            <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Başlık *</label>
            <input type="text" name="title" required
                   value="<?= htmlspecialchars($page['title'] ?? '') ?>"
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Slug (URL)</label>
            <input type="text" name="slug"
                   value="<?= htmlspecialchars($page['slug'] ?? '') ?>"
                   placeholder="Boş bırakılırsa başlıktan üretilir"
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white font-mono text-sm focus:border-blue-500 focus:outline-none">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-5">
        <div>
            <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Şablon</label>
            <select name="template"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                <option value="default" <?= ($page['template'] ?? '') === 'default' ? 'selected' : '' ?>>📄 Standart Sayfa</option>
                <option value="contact" <?= ($page['template'] ?? '') === 'contact' ? 'selected' : '' ?>>📬 İletişim Formu</option>
                <option value="full"    <?= ($page['template'] ?? '') === 'full' ? 'selected' : '' ?>>📏 Tam Genişlik</option>
            </select>
            <p class="text-xs text-gray-500 mt-1">İletişim şablonu otomatik form gösterir</p>
        </div>
        <div>
            <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Sıra</label>
            <input type="number" name="sort_order" value="<?= (int)($page['sort_order'] ?? 0) ?>"
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
        </div>
    </div>

    <div>
        <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">SEO Açıklama (meta description)</label>
        <input type="text" name="meta_description" maxlength="160"
               value="<?= htmlspecialchars($page['meta_description'] ?? '') ?>"
               placeholder="150-160 karakter arası önerilir"
               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
    </div>

    <div>
        <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">İçerik</label>
        <div id="page-content-editor"><?= $page['content'] ?? '' ?></div>
        <input type="hidden" name="content" id="page-content-input" value="<?= htmlspecialchars($page['content'] ?? '') ?>">
    </div>

    <div class="grid grid-cols-2 gap-5 pt-4 border-t border-gray-800">
        <label class="flex items-center gap-2 text-sm text-gray-300">
            <input type="checkbox" name="active" value="1" <?= !empty($page['active']) ? 'checked' : '' ?>
                   class="rounded bg-gray-800 border-gray-700">
            Aktif (sitede göster)
        </label>
        <label class="flex items-center gap-2 text-sm text-gray-300">
            <input type="checkbox" name="show_in_menu" value="1" <?= !empty($page['show_in_menu']) ? 'checked' : '' ?>
                   class="rounded bg-gray-800 border-gray-700">
            Navigasyon menüsünde göster
        </label>
    </div>

    <div class="flex justify-end gap-3 pt-4 border-t border-gray-800">
        <a href="/public/admin/pages" class="px-5 py-2 text-gray-400 hover:text-white text-sm">İptal</a>
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-6 py-2 rounded-lg">
            💾 Kaydet
        </button>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
const quill = new Quill('#page-content-editor', {
    theme: 'snow',
    placeholder: 'Sayfa içeriği…',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            ['blockquote'],
            ['link', 'image'],
            ['clean']
        ]
    }
});

document.getElementById('pageForm').addEventListener('submit', () => {
    document.getElementById('page-content-input').value = quill.root.innerHTML;
});
</script>
