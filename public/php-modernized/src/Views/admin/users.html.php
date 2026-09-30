<?php if (!empty($flash)): ?>
    <div class="mb-6 p-4 bg-blue-500/10 border border-blue-500/30 text-blue-400 rounded-lg">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<div class="bg-gray-900 border border-gray-800 rounded-xl p-6 mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-lg font-bold text-white">👥 Kullanıcılar (<?= count($users) ?>)</h2>
        <p class="text-xs text-gray-500 mt-1">Admin / Editör / Yazar rolleri</p>
    </div>
    <a href="/public/admin/users/create"
       class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-lg">
        + Yeni Kullanıcı
    </a>
</div>

<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
            <tr>
                <th class="text-left p-4">#</th>
                <th class="text-left p-4">Kullanıcı</th>
                <th class="text-left p-4">E-posta</th>
                <th class="text-left p-4">Rol</th>
                <th class="text-left p-4">Kayıt</th>
                <th class="text-right p-4">İşlem</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
            <tr><td colspan="6" class="text-center p-8 text-gray-600 italic">Kullanıcı yok.</td></tr>
        <?php else: foreach ($users as $u): ?>
            <?php
            $isSelf = (int)$u['user_id'] === (int)($_SESSION['user_id'] ?? 0);
            $level = (int)$u['user_level'];
            $roleBadge = match($level) {
                2 => ['👑 Admin',  'bg-red-500/20 text-red-400'],
                1 => ['✏️ Editör', 'bg-blue-500/20 text-blue-400'],
                default => ['👤 Üye', 'bg-gray-700 text-gray-400'],
            };
            ?>
            <tr class="border-t border-gray-800 hover:bg-gray-800/50">
                <td class="p-4 text-gray-500"><?= (int)$u['user_id'] ?></td>
                <td class="p-4 text-white font-bold">
                    <?= htmlspecialchars($u['username']) ?>
                    <?php if ($isSelf): ?>
                        <span class="ml-2 px-2 py-0.5 bg-green-500/20 text-green-400 text-xs rounded">Siz</span>
                    <?php endif; ?>
                </td>
                <td class="p-4 text-gray-400"><?= htmlspecialchars($u['user_email']) ?></td>
                <td class="p-4">
                    <span class="px-2 py-1 rounded text-xs <?= $roleBadge[1] ?>"><?= $roleBadge[0] ?></span>
                </td>
                <td class="p-4 text-gray-500"><?= date('d.m.Y', (int)$u['created_at']) ?></td>
                <td class="p-4 text-right space-x-2 whitespace-nowrap">
                    <a href="/public/admin/users/edit?id=<?= (int)$u['user_id'] ?>"
                       class="text-blue-400 hover:text-blue-300 text-xs">✏️ Düzenle</a>
                    <?php if (!$isSelf): ?>
                        <a href="/public/admin/users/delete?id=<?= (int)$u['user_id'] ?>"
                           onclick="return confirm('<?= htmlspecialchars($u['username']) ?> kullanıcısını silmek istediğinize emin misiniz?')"
                           class="text-red-400 hover:text-red-300 text-xs">🗑️ Sil</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
