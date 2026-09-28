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
    .ql-container.ql-snow { background: #1f2937; border-color: #374151 !important; border-radius: 0 0 8px 8px; color: #e5e7eb; font-size: 14px; min-height: 200px; }
    .ql-editor.ql-blank::before { color: #6b7280; font-style: normal; }
    .ql-snow .ql-stroke { stroke: #9ca3af; }
    .ql-snow .ql-fill { fill: #9ca3af; }
    .ql-snow .ql-picker { color: #9ca3af; }
    .ql-snow .ql-picker-options { background: #1f2937; border-color: #374151; }
    .ql-snow .ql-picker-item { color: #e5e7eb; }
</style>

<form method="post" enctype="multipart/form-data" id="blockForm" class="bg-gray-900 border border-gray-800 rounded-xl p-8 space-y-5">

    <div class="grid grid-cols-2 gap-5">
        <div>
            <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Başlık *</label>
            <input type="text" name="title" required value="<?= htmlspecialchars($block['title'] ?? '') ?>"
                   placeholder="Örn: Sosyal Medya"
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Pozisyon *</label>
            <select name="bposition" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                <option value="l" <?= ($block['bposition'] ?? '') === 'l' ? 'selected' : '' ?>>⬅️ Sol Sütun</option>
                <option value="r" <?= ($block['bposition'] ?? 'r') === 'r' ? 'selected' : '' ?>>➡️ Sağ Sütun</option>
                <option value="c" <?= ($block['bposition'] ?? '') === 'c' ? 'selected' : '' ?>>⬆️ Üst Sütun</option>
            </select>
        </div>
    </div>

    <div>
        <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Blok Tipi *</label>
        <select name="block_type" id="blockTypeSelect"
                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
            <option value="html"   <?= ($block['block_type'] ?? '') === 'html'   ? 'selected' : '' ?>>📝 HTML İçerik</option>
            <option value="social" <?= ($block['block_type'] ?? '') === 'social' ? 'selected' : '' ?>>🔗 Sosyal Medya</option>
            <option value="ad"     <?= ($block['block_type'] ?? '') === 'ad'     ? 'selected' : '' ?>>🖼️ Reklam / Görsel</option>
            <option value="php"    <?= ($block['block_type'] ?? '') === 'php'    ? 'selected' : '' ?>>📄 PHP Dosya</option>
        </select>
    </div>

    <!-- HTML TİPİ -->
    <div class="block-type-panel" data-type="html">
        <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">İçerik (HTML)</label>
        <div id="html-editor-container"><?= $block['content'] ?? '' ?></div>
        <input type="hidden" name="content" id="content-input" value="<?= htmlspecialchars($block['content'] ?? '') ?>">
    </div>

    <!-- SOSYAL MEDYA TİPİ -->
    <div class="block-type-panel" data-type="social">
        <?php $cfg = json_decode($block['config'] ?? '{}', true) ?: []; ?>
        <label class="block text-xs uppercase tracking-wider text-gray-500 mb-3">Sosyal Medya Linkleri (boş bırakılanlar gizlenir)</label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <?php
            $socials = [
                'facebook'  => ['📘', 'Facebook',  'https://facebook.com/...'],
                'twitter'   => ['𝕏',  'Twitter/X', 'https://x.com/...'],
                'instagram' => ['📷', 'Instagram', 'https://instagram.com/...'],
                'youtube'   => ['▶️', 'YouTube',   'https://youtube.com/@...'],
                'linkedin'  => ['💼', 'LinkedIn',  'https://linkedin.com/in/...'],
                'github'    => ['🐙', 'GitHub',    'https://github.com/...'],
                'tiktok'    => ['🎵', 'TikTok',    'https://tiktok.com/@...'],
                'whatsapp'  => ['💬', 'WhatsApp',  'https://wa.me/90...'],
                'telegram'  => ['✈️', 'Telegram',  'https://t.me/...'],
            ];
            foreach ($socials as $key => $meta):
            ?>
                <div class="flex items-center gap-2">
                    <span class="text-xl w-8 text-center"><?= $meta[0] ?></span>
                    <input type="text" name="social_<?= $key ?>"
                           value="<?= htmlspecialchars($cfg[$key] ?? '') ?>"
                           placeholder="<?= $meta[2] ?>"
                           class="flex-1 bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:border-blue-500 focus:outline-none">
                </div>
            <?php endforeach; ?>
        </div>
        <p class="text-xs text-gray-500 mt-3">💡 Sadece doldurduğunuz sosyal ağlar sitede gösterilir.</p>
    </div>

    <!-- REKLAM TİPİ -->
    <div class="block-type-panel" data-type="ad">
        <?php if (!empty($block['image'])): ?>
            <div class="mb-3 flex items-center gap-4 p-3 bg-gray-800 rounded-lg border border-gray-700">
                <img src="<?= htmlspecialchars($block['image']) ?>" class="w-32 h-20 object-contain bg-black rounded">
                <label class="text-xs text-red-400 cursor-pointer">
                    <input type="checkbox" name="remove_image" value="1" class="mr-1"> Görseli kaldır
                </label>
            </div>
        <?php endif; ?>
        <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Reklam Görseli</label>
        <input type="file" name="ad_image" accept="image/*"
               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-600 file:text-white file:font-bold file:cursor-pointer">
        <p class="text-xs text-gray-500 mt-1">Otomatik olarak max 1600px'e küçültülür</p>

        <label class="block text-xs uppercase tracking-wider text-gray-500 mt-4 mb-2">Tıklanınca Gidilecek URL (opsiyonel)</label>
        <input type="text" name="link_url" value="<?= htmlspecialchars($block['link_url'] ?? '') ?>"
               placeholder="https://reklam-veren.com"
               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
    </div>

    <!-- PHP TİPİ -->
    <div class="block-type-panel" data-type="php">
        <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">PHP Blok Dosyası</label>
        <select name="blockfile"
                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
            <option value="">— Dosya Seçin —</option>
            <?php foreach ($availableFiles ?? [] as $f): ?>
                <option value="<?= htmlspecialchars($f['file']) ?>"
                    <?= ($block['blockfile'] ?? '') === $f['file'] ? 'selected' : '' ?>>
                    📄 <?= htmlspecialchars($f['file']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="text-xs text-gray-500 mt-1">
            📁 <code>src/Blocks/</code> klasörüne yeni <code>.php</code> dosyaları ekleyerek blok geliştirebilirsiniz.
        </p>
    </div>

    <!-- SIRA + AKTİF -->
    <div class="grid grid-cols-2 gap-5">
        <div>
            <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Sıra (küçük yukarıda)</label>
            <input type="number" name="weight" value="<?= (int)($block['weight'] ?? 0) ?>"
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
        </div>
        <div class="flex items-end">
            <label class="flex items-center gap-2 text-sm text-gray-300 pb-3">
                <input type="checkbox" name="active" value="1" <?= !empty($block['active']) ? 'checked' : '' ?>
                       class="rounded bg-gray-800 border-gray-700">
                Aktif (sitede göster)
            </label>
        </div>
    </div>

    <div class="flex justify-end gap-3 pt-4 border-t border-gray-800">
        <a href="/public/admin/blocks" class="px-5 py-2 text-gray-400 hover:text-white text-sm">İptal</a>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-6 py-2 rounded-lg">💾 Kaydet</button>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
const htmlContainer = document.getElementById('html-editor-container');
let quill = null;
if (htmlContainer) {
    quill = new Quill(htmlContainer, {
        theme: 'snow',
        placeholder: 'Blok içeriğini yazın…',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['link'],
                ['clean']
            ]
        }
    });
}

function updatePanels() {
    const selected = document.getElementById('blockTypeSelect').value;
    document.querySelectorAll('.block-type-panel').forEach(p => {
        p.style.display = p.dataset.type === selected ? '' : 'none';
    });
}
document.getElementById('blockTypeSelect').addEventListener('change', updatePanels);
updatePanels();

document.getElementById('blockForm').addEventListener('submit', () => {
    if (quill) document.getElementById('content-input').value = quill.root.innerHTML;
});
</script>
