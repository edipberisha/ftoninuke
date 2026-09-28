<div class="bg-gray-900 border border-gray-800 rounded-xl p-8">
    <h2 class="text-lg font-bold text-white mb-6">⚙️ Sistem Ayarları</h2>
    <?php if (empty($config)): ?>
        <p class="text-gray-500 italic">Config tablosunda kayıt yok.</p>
    <?php else: ?>
        <div class="space-y-3 text-sm">
            <?php foreach ($config as $key => $value): ?>
                <div class="flex justify-between border-b border-gray-800 pb-2">
                    <span class="text-gray-400 font-mono"><?= htmlspecialchars((string)$key) ?></span>
                    <span class="text-white"><?= htmlspecialchars((string)$value) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>