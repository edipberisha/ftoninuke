<?php
/**
 * Modern Media Library View
 */
?>
<div class="media-library-admin p-8 bg-gray-950 min-h-screen text-gray-300">
    <header class="flex justify-between items-center mb-10 pb-6 border-b border-gray-800">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-white uppercase italic">Media <span class="text-blue-600">Assets</span></h1>
            <p class="text-xs text-gray-500 mt-1">Manage, upload and optimize your platform's visual memory.</p>
        </div>
        <div class="flex gap-4">
             <button id="upload-trigger" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-6 py-2 rounded-lg transition-all flex items-center gap-2">
                 <i data-lucide="upload-cloud" class="w-4 h-4"></i> Upload New
             </button>
        </div>
    </header>

    <!-- Drag & Drop Zone -->
    <div id="drop-zone" class="hidden mb-12 border-2 border-dashed border-blue-500/30 bg-blue-500/5 rounded-2xl p-12 text-center transition-all">
        <input type="file" id="file-input" class="hidden" multiple accept="image/*">
        <div class="flex flex-col items-center">
            <div class="w-16 h-16 bg-blue-500/10 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="image-plus" class="w-8 h-8 text-blue-500"></i>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Drop your images here</h3>
            <p class="text-sm text-gray-500">or click to browse from your computer</p>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
        <?php foreach ($mediaList as $item): ?>
            <div class="media-card group relative bg-gray-900 border border-gray-800 rounded-xl overflow-hidden shadow-sm hover:border-blue-500/50 transition-all">
                <div class="aspect-square bg-black/40 flex items-center justify-center overflow-hidden">
                    <img src="<?= htmlspecialchars($item['path']) ?>" 
                         alt="<?= htmlspecialchars($item['filename']) ?>" 
                         class="object-cover w-full h-full transform group-hover:scale-110 transition-transform duration-500"
                    >
                </div>
                
                <!-- Overlay Actions -->
                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                    <button class="p-2 bg-white/10 hover:bg-white/20 rounded-lg text-white" title="View Detail">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                    <a href="/public/admin/media-delete.php?id=<?= $item['id'] ?>" 
                       class="p-2 bg-red-500/20 hover:bg-red-500/40 rounded-lg text-red-500" 
                       onclick="return confirm('Secure delete this asset?')"
                       title="Delete Forever">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </a>
                </div>

                <div class="p-3">
                    <div class="text-[10px] font-mono text-gray-500 truncate mb-1"><?= htmlspecialchars($item['filename']) ?></div>
                    <div class="text-[9px] text-gray-600 uppercase tracking-widest"><?= round($item['size'] / 1024, 1) ?> KB</div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    // Simple POC for Drag and Drop logic
    const dropZone = document.getElementById('drop-zone');
    const uploadTrigger = document.getElementById('upload-trigger');
    const fileInput = document.getElementById('file-input');

    uploadTrigger.addEventListener('click', () => fileInput.click());

    // Show drop zone on drag enter (mock)
    document.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.remove('hidden');
    });

    fileInput.addEventListener('change', async (e) => {
        const files = e.target.files;
        if (files.length > 0) {
            const formData = new FormData();
            formData.append('file', files[0]);
            formData.append('csrf_token', '<?= $csrfToken ?>');

            try {
                const response = await fetch('/admin/media-upload.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    window.location.reload();
                } else {
                    alert('Upload failed: ' + result.error);
                }
            } catch (err) {
                console.error(err);
            }
        }
    });

    lucide.createIcons();
</script>
