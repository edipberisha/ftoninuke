<?php if (!empty($flash)): ?>
    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 text-green-400 rounded-lg">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<style>
    .settings-tab {
        padding: 12px 20px;
        border-radius: 8px 8px 0 0;
        background: transparent;
        color: #9ca3af;
        font-weight: 600;
        font-size: .9em;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all .2s;
    }
    .settings-tab:hover { color: #fff; background: #1f2937; }
    .settings-tab.active { color: #3b82f6; border-bottom-color: #3b82f6; }
    .settings-panel { display: none; }
    .settings-panel.active { display: block; }

    .toggle {
        position: relative; display: inline-block;
        width: 48px; height: 26px;
    }
    .toggle input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute; cursor: pointer;
        inset: 0; background: #4b5563;
        transition: .3s; border-radius: 34px;
    }
    .toggle-slider:before {
        content: ''; position: absolute;
        height: 20px; width: 20px;
        left: 3px; bottom: 3px;
        background: white; transition: .3s;
        border-radius: 50%;
    }
    .toggle input:checked + .toggle-slider { background: #3b82f6; }
    .toggle input:checked + .toggle-slider:before { transform: translateX(22px); }
</style>

<form method="post" class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">

    <!-- Sekmeler -->
    <div class="flex border-b border-gray-800 bg-gray-900">
        <button type="button" class="settings-tab active" data-tab="general">⚙️ Genel</button>
        <button type="button" class="settings-tab" data-tab="membership">👥 Üyelik</button>
        <button type="button" class="settings-tab" data-tab="comments">💬 Yorumlar</button>
    </div>

    <div class="p-8">

        <!-- GENEL -->
        <div class="settings-panel active" data-panel="general">
            <h3 class="text-lg font-bold text-white mb-6">Genel Ayarlar</h3>

            <div class="space-y-5">
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Site Adı</label>
                    <input type="text" name="sitename"
                           value="<?= htmlspecialchars($config['sitename'] ?? 'FtoniNuke') ?>"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Aktif Tema</label>
                    <input type="text" name="theme"
                           value="<?= htmlspecialchars($config['theme'] ?? 'default') ?>"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                    <p class="text-xs text-gray-500 mt-1">Tema sistemi yakında gelecek.</p>
                </div>
            </div>
        </div>

        <!-- ÜYELİK -->
        <div class="settings-panel" data-panel="membership">
            <h3 class="text-lg font-bold text-white mb-6">Üyelik Ayarları</h3>

            <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4 mb-6">
                <p class="text-yellow-400 text-sm">
                    ⚠️ <strong>Önemli:</strong> Kayıt açıldığında <strong>KVKK/GDPR</strong> yükümlülükleri site sahibine aittir.
                    E-posta doğrulama, şifre sıfırlama gibi özellikler için SMTP yapılandırması gerekir.
                </p>
            </div>

            <div class="space-y-5">
                <div class="flex items-center justify-between bg-gray-800 rounded-lg p-4">
                    <div>
                        <div class="text-white font-bold mb-1">Frontend Kayıt</div>
                        <div class="text-xs text-gray-500">Ziyaretçiler yeni hesap açabilir mi?</div>
                    </div>
                    <label class="toggle">
                        <input type="checkbox" name="registration_enabled"
                               <?= !empty($config['registration_enabled']) ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="flex items-center justify-between bg-gray-800 rounded-lg p-4">
                    <div>
                        <div class="text-white font-bold mb-1">E-posta Doğrulaması</div>
                        <div class="text-xs text-gray-500">Kayıt sonrası e-posta doğrulaması zorunlu mu?</div>
                    </div>
                    <label class="toggle">
                        <input type="checkbox" name="email_verification"
                               <?= !empty($config['email_verification']) ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Yeni Üye Varsayılan Rolü</label>
                    <select name="default_user_level"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                        <option value="1" <?= ((int)($config['default_user_level'] ?? 1) === 1) ? 'selected' : '' ?>>
                            👤 Üye (yorum yapabilir)
                        </option>
                        <option value="0" <?= ((int)($config['default_user_level'] ?? 1) === 0) ? 'selected' : '' ?>>
                            ✍️ Yazar (haber ekleyebilir)
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <!-- YORUMLAR -->
        <div class="settings-panel" data-panel="comments">
            <h3 class="text-lg font-bold text-white mb-6">Yorum Ayarları</h3>

            <div class="space-y-5">
                <div class="flex items-center justify-between bg-gray-800 rounded-lg p-4">
                    <div>
                        <div class="text-white font-bold mb-1">Yorum Onayı</div>
                        <div class="text-xs text-gray-500">Yorumlar admin onayı beklesin mi?</div>
                    </div>
                    <label class="toggle">
                        <input type="checkbox" name="comments_require_approval"
                               <?= !empty($config['comments_require_approval']) ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="flex items-center justify-between bg-gray-800 rounded-lg p-4">
                    <div>
                        <div class="text-white font-bold mb-1">CAPTCHA Koruması</div>
                        <div class="text-xs text-gray-500">Yorum formunda matematik CAPTCHA aktif mi?</div>
                    </div>
                    <label class="toggle">
                        <input type="checkbox" name="captcha_enabled"
                               <?= !empty($config['captcha_enabled']) ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Saatlik Yorum Limiti (IP başına)</label>
                    <input type="number" name="comment_rate_limit" min="1" max="100"
                           value="<?= (int)($config['comment_rate_limit'] ?? 5) ?>"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                    <p class="text-xs text-gray-500 mt-1">Aynı IP'den saatte max kaç yorum gönderilebilir?</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Kaydet -->
    <div class="flex justify-end gap-3 p-6 border-t border-gray-800 bg-gray-900">
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-6 py-2 rounded-lg">
            💾 Ayarları Kaydet
        </button>
    </div>
</form>

<script>
document.querySelectorAll('.settings-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.querySelector(`[data-panel="${tab.dataset.tab}"]`).classList.add('active');
    });
});
</script>
