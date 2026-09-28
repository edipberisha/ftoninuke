<?php if (!empty($errors)): ?>
    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-lg">
        <ul class="list-disc list-inside space-y-1">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
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
    .ql-snow .ql-active .ql-stroke { stroke: #3b82f6 !important; }
    .ql-snow .ql-active .ql-fill { fill: #3b82f6 !important; }
    .ql-editor img { max-width: 100%; border-radius: 6px; margin: 8px 0; }

    #media-picker-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.85); z-index: 9999; padding: 40px; overflow-y: auto; }
    #media-picker-modal.open { display: block; }
    #media-picker-modal .modal-content { background: #111827; border: 1px solid #374151; border-radius: 12px; padding: 24px; max-width: 1100px; margin: 0 auto; }
    #media-picker-modal .media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; max-height: 65vh; overflow-y: auto; padding: 4px; }
    #media-picker-modal .media-item { cursor: pointer; border: 2px solid transparent; border-radius: 8px; overflow: hidden; background: #1f2937; transition: border-color .15s; }
    #media-picker-modal .media-item:hover { border-color: #3b82f6; }
    #media-picker-modal .media-item img { width: 100%; height: 120px; object-fit: cover; display: block; }
    #media-picker-modal .media-item .name { padding: 6px 8px; font-size: 11px; color: #9ca3af; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .upload-spinner { display: inline-block; width: 16px; height: 16px; border: 2px solid #3b82f6; border-top-color: transparent; border-radius: 50%; animation: spin .6s linear infinite; vertical-align: middle; margin-left: 6px; }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<form method="post" enctype="multipart/form-data" id="newsForm" class="bg-gray-900 border border-gray-800 rounded-xl p-8 space-y-6">

    <!-- BAŞLIK -->
    <div>
        <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Başlık *</label>
        <input type="text" name="title" required value="<?= htmlspecialchars($data['title'] ?? '') ?>"
               placeholder="Haber başlığı…"
               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white text-lg font-bold focus:border-blue-500 focus:outline-none">
    </div>

    <!-- ÖNE ÇIKAN GÖRSEL -->
    <div>
        <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Öne Çıkan Görsel</label>
        <input type="hidden" name="image" id="featured-image-input" value="<?= htmlspecialchars($data['image'] ?? '') ?>">

        <div id="featured-preview" class="<?= empty($data['image']) ? 'hidden' : '' ?> mb-3 flex items-center gap-4 p-3 bg-gray-800 rounded-lg border border-gray-700">
            <img id="featured-preview-img" src="<?= htmlspecialchars($data['image'] ?? '') ?>" alt=""
                 class="w-32 h-32 object-cover rounded-lg border border-gray-700">
            <div class="flex-1">
                <p class="text-xs text-gray-400 mb-2">Seçili öne çıkan görsel</p>
                <button type="button" onclick="clearFeatured()" class="text-xs text-red-400 hover:text-red-300">🗑️ Kaldır</button>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="button" onclick="openMediaPicker()"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2 rounded-lg">
                📁 Kütüphaneden Seç
            </button>
            <label class="bg-gray-700 hover:bg-gray-600 text-white text-sm font-bold px-5 py-2 rounded-lg cursor-pointer">
                <span id="featured-upload-label">⬆️ Bilgisayardan Yükle</span>
                <input type="file" id="featured-file" accept="image/*" class="hidden">
            </label>
        </div>
        <p class="text-xs text-gray-500 mt-2">Görseller otomatik olarak max 1600px'e küçültülür · JPG, PNG, WEBP, GIF</p>
    </div>

    <!-- KATEGORİ + KAYNAK -->
    <div class="grid grid-cols-2 gap-5">
        <div>
            <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Kategori *</label>
            <select name="topic" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                <option value="">— Kategori Seçin —</option>
                <?php foreach ($topics as $t): ?>
                    <option value="<?= (int)$t['topic_id'] ?>" <?= ((int)($data['topic'] ?? 0) === (int)$t['topic_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['topic_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Kaynak / Informant</label>
            <input type="text" name="informant" value="<?= htmlspecialchars($data['informant'] ?? '') ?>"
                   placeholder="örn: AA, Reuters…"
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
        </div>
    </div>

    <!-- ÖZET -->
    <div>
        <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Özet</label>
        <textarea name="hometext" rows="3" placeholder="Ana sayfada gösterilecek kısa özet…"
                  class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none"><?= htmlspecialchars($data['hometext'] ?? '') ?></textarea>
    </div>

    <!-- İÇERİK -->
    <div>
        <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">İçerik</label>
        <div id="editor-container"><?= $data['bodytext'] ?? '' ?></div>
        <input type="hidden" name="bodytext" id="bodytext-input" value="<?= htmlspecialchars($data['bodytext'] ?? '') ?>">
        <p class="text-xs text-gray-500 mt-2">💡 Editör araç çubuğundaki 🖼️ ile içeriğe görsel ekleyin.</p>
    </div>

    <!-- BUTONLAR -->
    <div class="flex justify-end gap-3 pt-6 border-t border-gray-800">
        <a href="/public/admin/news" class="px-5 py-2 text-gray-400 hover:text-white text-sm">İptal</a>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-6 py-2 rounded-lg">💾 Kaydet</button>
    </div>
</form>

<!-- MEDYA PICKER MODAL -->
<div id="media-picker-modal">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-white text-lg font-bold">📁 Medya Kütüphanesi</h3>
            <button type="button" onclick="closeMediaPicker()" class="text-gray-400 hover:text-white text-2xl leading-none">×</button>
        </div>
        <div id="media-grid" class="media-grid">
            <p class="text-gray-500 italic">Yükleniyor…</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
/* ═══════════════════════════════════════════════════════════
   1. GÖRSEL RESIZE (Client-side, GD gerektirmez)
   ═══════════════════════════════════════════════════════════ */
async function resizeImage(file, maxDimension = 1600, quality = 0.85) {
    return new Promise((resolve) => {
        // Görsel değilse direkt geçir
        if (!file.type.startsWith('image/')) {
            resolve(file);
            return;
        }

        // GIF animasyonunu bozmamak için resize etme
        if (file.type === 'image/gif') {
            resolve(file);
            return;
        }

        const img = new Image();
        const url = URL.createObjectURL(file);

        img.onload = () => {
            URL.revokeObjectURL(url);

            let { width, height } = img;

            // Zaten küçükse resize etme
            if (width <= maxDimension && height <= maxDimension) {
                resolve(file);
                return;
            }

            // Oranı koru
            if (width > height) {
                if (width > maxDimension) {
                    height = Math.round(height * maxDimension / width);
                    width = maxDimension;
                }
            } else {
                if (height > maxDimension) {
                    width = Math.round(width * maxDimension / height);
                    height = maxDimension;
                }
            }

            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;

            const ctx = canvas.getContext('2d');
            ctx.imageSmoothingQuality = 'high';
            ctx.drawImage(img, 0, 0, width, height);

            canvas.toBlob((blob) => {
                if (!blob) { resolve(file); return; }
                // Yeni dosya adı
                const newName = file.name.replace(/\.[^.]+$/, '') + '.jpg';
                const resized = new File([blob], newName, { type: 'image/jpeg', lastModified: Date.now() });
                console.log(`[Resize] ${file.name} (${(file.size/1024).toFixed(0)}KB) → ${newName} (${(blob.size/1024).toFixed(0)}KB, ${width}x${height})`);
                resolve(resized);
            }, 'image/jpeg', quality);
        };

        img.onerror = () => resolve(file);
        img.src = url;
    });
}

/* ═══════════════════════════════════════════════════════════
   2. YÜKLEME FONKSİYONU (resize + upload)
   ═══════════════════════════════════════════════════════════ */
async function uploadFile(file) {
    // Önce tarayıcıda resize
    const resized = await resizeImage(file);

    const fd = new FormData();
    fd.append('file', resized);

    const res = await fetch('/public/api/upload', {
        method: 'POST',
        body: fd,
        credentials: 'same-origin'
    });

    if (!res.ok) throw new Error('HTTP ' + res.status);
    return await res.json();
}

/* ═══════════════════════════════════════════════════════════
   3. QUILL EDİTÖR
   ═══════════════════════════════════════════════════════════ */
const quill = new Quill('#editor-container', {
    theme: 'snow',
    placeholder: 'Haberin tam metnini buraya yazın…',
    modules: {
        toolbar: {
            container: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['blockquote', 'code-block'],
                ['link', 'image'],
                ['clean']
            ],
            handlers: { image: imageHandler }
        }
    }
});

document.getElementById('newsForm').addEventListener('submit', function() {
    const html = quill.root.innerHTML;
    document.getElementById('bodytext-input').value = quill.getText().trim().length === 0 ? '' : html;
});

async function imageHandler() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.click();

    input.onchange = async () => {
        const file = input.files[0];
        if (!file) return;

        try {
            const range = quill.getSelection(true) || { index: quill.getLength() };

            // Geçici placeholder göster
            quill.insertText(range.index, '⏳ Yükleniyor…', 'italic');
            quill.setSelection(range.index + 15);

            const data = await uploadFile(file);

            // Placeholder'ı sil
            quill.deleteText(range.index, 15);

            if (!data.location) {
                alert('Yükleme başarısız: ' + JSON.stringify(data));
                return;
            }

            quill.insertEmbed(range.index, 'image', data.location);
            quill.setSelection(range.index + 1);
        } catch (e) {
            alert('Hata: ' + e.message);
        }
    };
}

/* ═══════════════════════════════════════════════════════════
   4. ÖNE ÇIKAN GÖRSEL
   ═══════════════════════════════════════════════════════════ */
document.getElementById('featured-file').addEventListener('change', async function() {
    const file = this.files[0];
    if (!file) return;

    const label = document.getElementById('featured-upload-label');
    const original = label.textContent;
    label.innerHTML = 'Yükleniyor… <span class="upload-spinner"></span>';

    try {
        const data = await uploadFile(file);
        if (!data.location) {
            alert('Yükleme başarısız');
            label.textContent = original;
            return;
        }
        document.getElementById('featured-image-input').value = data.location;
        document.getElementById('featured-preview-img').src = data.location;
        document.getElementById('featured-preview').classList.remove('hidden');
        label.textContent = '✓ Yüklendi';
        setTimeout(() => { label.textContent = original; }, 2000);
        mediaCache = null;
    } catch (e) {
        alert('Hata: ' + e.message);
        label.textContent = original;
    }
});

function clearFeatured() {
    document.getElementById('featured-image-input').value = '';
    document.getElementById('featured-preview').classList.add('hidden');
    document.getElementById('featured-file').value = '';
}

/* ═══════════════════════════════════════════════════════════
   5. MEDYA PICKER
   ═══════════════════════════════════════════════════════════ */
let mediaCache = null;

async function openMediaPicker() {
    document.getElementById('media-picker-modal').classList.add('open');
    const grid = document.getElementById('media-grid');

    if (!mediaCache) {
        try {
            const res = await fetch('/public/api/media', { credentials: 'same-origin' });
            const data = await res.json();
            mediaCache = data.files || [];
        } catch (e) {
            grid.innerHTML = '<p class="text-red-400">Hata: ' + e.message + '</p>';
            return;
        }
    }

    if (mediaCache.length === 0) {
        grid.innerHTML = '<p class="text-gray-500 italic col-span-full text-center py-8">Henüz medya yok.</p>';
        return;
    }

    grid.innerHTML = mediaCache.map(f => `
        <div class="media-item" onclick="selectFeatured('${f.path.replace(/'/g, "\\'")}')">
            <img src="${f.path}" alt="" loading="lazy">
            <div class="name">${f.filename || ''}</div>
        </div>
    `).join('');
}

function closeMediaPicker() {
    document.getElementById('media-picker-modal').classList.remove('open');
}

function selectFeatured(path) {
    document.getElementById('featured-image-input').value = path;
    document.getElementById('featured-preview-img').src = path;
    document.getElementById('featured-preview').classList.remove('hidden');
    closeMediaPicker();
}

document.getElementById('media-picker-modal').addEventListener('click', (e) => {
    if (e.target.id === 'media-picker-modal') closeMediaPicker();
});
</script>
