<?php if (!empty($errors)): ?>
    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-lg">
        <ul class="list-disc list-inside">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" class="bg-gray-900 border border-gray-800 rounded-xl p-8 space-y-5 max-w-2xl">

    <div>
        <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Kullanıcı Adı *</label>
        <input type="text" name="username" required
               value="<?= htmlspecialchars($data['username'] ?? '') ?>"
               placeholder="örn: ahmet_yilmaz"
               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
        <p class="text-xs text-gray-500 mt-1">3+ karakter, harf/rakam/nokta/tire/alt çizgi</p>
    </div>

    <div>
        <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">E-posta *</label>
        <input type="email" name="user_email" required
               value="<?= htmlspecialchars($data['user_email'] ?? '') ?>"
               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
    </div>

    <div>
        <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">
            Şifre <?= ($formMode ?? 'create') === 'create' ? '*' : '(değiştirmek için doldur)' ?>
        </label>
        <input type="password" name="password"
               <?= ($formMode ?? 'create') === 'create' ? 'required' : '' ?>
               placeholder="<?= ($formMode ?? 'create') === 'create' ? 'En az 6 karakter' : 'Boş bırakılırsa değişmez' ?>"
               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
    </div>

    <div>
        <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Rol *</label>
        <select name="user_level"
                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
            <option value="0" <?= ((int)($data['user_level'] ?? 1) === 0) ? 'selected' : '' ?>>
                👤 Üye — sadece yorum yapabilir
            </option>
            <option value="1" <?= ((int)($data['user_level'] ?? 1) === 1) ? 'selected' : '' ?>>
                ✏️ Editör — haber + yorum yönetimi
            </option>
            <option value="2" <?= ((int)($data['user_level'] ?? 1) === 2) ? 'selected' : '' ?>>
                👑 Admin — tam yetki
            </option>
        </select>
    </div>

    <div class="flex justify-end gap-3 pt-4 border-t border-gray-800">
        <a href="/public/admin/users" class="px-5 py-2 text-gray-400 hover:text-white text-sm">İptal</a>
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-6 py-2 rounded-lg">
            💾 Kaydet
        </button>
    </div>
</form>
