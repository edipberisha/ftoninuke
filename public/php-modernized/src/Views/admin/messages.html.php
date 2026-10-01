<?php if (!empty($flash)): ?>
    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 text-green-400 rounded-lg">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<div class="bg-gray-900 border border-gray-800 rounded-xl p-6 mb-6">
    <h2 class="text-lg font-bold text-white">📬 İletişim Mesajları (<?= count($messages) ?>)</h2>
    <p class="text-xs text-gray-500 mt-1">İletişim formundan gelen mesajlar</p>
</div>

<?php if (empty($messages)): ?>
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-12 text-center text-gray-500 italic">
        Henüz mesaj yok.
    </div>
<?php else: ?>
    <div class="space-y-3">
        <?php foreach ($messages as $m): ?>
            <div class="bg-gray-900 border <?= $m['is_read'] ? 'border-gray-800' : 'border-yellow-500/30 bg-yellow-500/5' ?> rounded-xl p-5">
                <div class="flex justify-between items-start gap-4 flex-wrap mb-3">
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <strong class="text-white"><?= htmlspecialchars($m['name']) ?></strong>
                            <a href="mailto:<?= htmlspecialchars($m['email']) ?>" class="text-blue-400 text-sm hover:underline">
                                <?= htmlspecialchars($m['email']) ?>
                            </a>
                            <?php if (!$m['is_read']): ?>
                                <span class="px-2 py-0.5 bg-yellow-500 text-black text-xs font-bold rounded">YENİ</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($m['subject'])): ?>
                            <div class="text-sm text-gray-400 mt-1">📌 <?= htmlspecialchars($m['subject']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="text-xs text-gray-500 text-right">
                        <div><?= date('d.m.Y H:i', (int)$m['created_at']) ?></div>
                        <div class="font-mono"><?= htmlspecialchars($m['ip_address']) ?></div>
                    </div>
                </div>
                <div class="text-gray-300 text-sm bg-gray-800/50 rounded-lg p-4 mb-3 whitespace-pre-wrap"><?= htmlspecialchars($m['message']) ?></div>
                <div class="flex gap-2">
                    <?php if (!$m['is_read']): ?>
                        <a href="/public/admin/messages?read=<?= (int)$m['id'] ?>"
                           class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded">✓ Okundu İşaretle</a>
                    <?php endif; ?>
                    <a href="mailto:<?= htmlspecialchars($m['email']) ?>?subject=<?= urlencode('Re: ' . ($m['subject'] ?: 'Mesajınız')) ?>"
                       class="px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-white text-xs font-bold rounded">✉️ Yanıtla</a>
                    <a href="/public/admin/messages?delete=<?= (int)$m['id'] ?>"
                       onclick="return confirm('Bu mesajı silmek istediğinize emin misiniz?')"
                       class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded">🗑️ Sil</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
