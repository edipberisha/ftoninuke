<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
            <tr>
                <th class="text-left p-4">#</th>
                <th class="text-left p-4">Kullanıcı</th>
                <th class="text-left p-4">E-posta</th>
                <th class="text-left p-4">Seviye</th>
                <th class="text-left p-4">Kayıt</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
            <tr><td colspan="5" class="text-center p-8 text-gray-600 italic">Kullanıcı yok.</td></tr>
        <?php else: foreach ($users as $u): ?>
            <tr class="border-t border-gray-800 hover:bg-gray-800/50">
                <td class="p-4 text-gray-500"><?= (int)$u['user_id'] ?></td>
                <td class="p-4 text-white font-bold"><?= htmlspecialchars($u['username']) ?></td>
                <td class="p-4 text-gray-400"><?= htmlspecialchars($u['user_email']) ?></td>
                <td class="p-4">
                    <span class="px-2 py-1 rounded text-xs <?= ((int)$u['user_level'] >= 2) ? 'bg-blue-500/20 text-blue-400' : 'bg-gray-700 text-gray-400' ?>">
                        <?= ((int)$u['user_level'] >= 2) ? 'Admin' : 'Üye' ?>
                    </span>
                </td>
                <td class="p-4 text-gray-500"><?= date('d.m.Y', (int)$u['created_at']) ?></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>