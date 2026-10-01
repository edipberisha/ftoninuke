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
		<button type="button" class="settings-tab" data-tab="mail">📧 Mail</button>
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
		        <!-- MAIL -->
        <div class="settings-panel" data-panel="mail">
            <h3 class="text-lg font-bold text-white mb-6">📧 Mail Ayarları (SMTP)</h3>

            <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4 mb-6">
                <p class="text-blue-400 text-sm">
                    💡 <strong>Nasıl ayarlanır?</strong><br>
                    • <strong>Gmail:</strong> smtp.gmail.com, port 587, TLS, uygulama şifresi<br>
                    • <strong>Yandex:</strong> smtp.yandex.com, port 465, SSL<br>
                    • <strong>Outlook:</strong> smtp-mail.outlook.com, port 587, TLS<br>
                    • <strong>Kendi sunucunuz:</strong> mail.siteniz.com, port 587
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">SMTP Sunucu</label>
                    <input type="text" name="smtp_host"
                           value="<?= htmlspecialchars($config['smtp_host'] ?? '') ?>"
                           placeholder="smtp.gmail.com"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Port</label>
                    <input type="number" name="smtp_port"
                           value="<?= (int)($config['smtp_port'] ?? 587) ?>"
                           placeholder="587"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Kullanıcı Adı (E-posta)</label>
                    <input type="text" name="smtp_user"
                           value="<?= htmlspecialchars($config['smtp_user'] ?? '') ?>"
                           placeholder="siteniz@gmail.com"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Şifre</label>
                    <input type="password" name="smtp_pass"
                           value="<?= htmlspecialchars($config['smtp_pass'] ?? '') ?>"
                           placeholder="Uygulama şifresi"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Güvenlik</label>
                <select name="smtp_secure"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                    <option value="tls" <?= ($config['smtp_secure'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS (port 587)</option>
                    <option value="ssl" <?= ($config['smtp_secure'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL (port 465)</option>
                    <option value=""    <?= ($config['smtp_secure'] ?? '') === '' ? 'selected' : '' ?>>Yok (port 25)</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-4 border-t border-gray-800">
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Gönderen Adı</label>
                    <input type="text" name="smtp_from_name"
                           value="<?= htmlspecialchars($config['smtp_from_name'] ?? 'FtoniNuke') ?>"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Gönderen E-posta</label>
                    <input type="email" name="smtp_from_email"
                           value="<?= htmlspecialchars($config['smtp_from_email'] ?? '') ?>"
                           placeholder="noreply@siteniz.com"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-4 border-t border-gray-800">
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">📥 İletişim Formu Mesajları Hangi Adrese Gitsin?</label>
                <input type="email" name="contact_email"
                       value="<?= htmlspecialchars($config['contact_email'] ?? '') ?>"
                       placeholder="info@siteniz.com"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                <p class="text-xs text-gray-500 mt-1">Boş bırakılırsa "Gönderen E-posta" adresi kullanılır.</p>
            </div>

            <!-- TEST MAIL -->
            <div class="mt-6 pt-6 border-t border-gray-800">
                <h4 class="text-white font-bold mb-3">🧪 Test Maili Gönder</h4>
                <div class="flex gap-3">
                    <input type="email" id="test-mail-to" placeholder="test@example.com"
                           class="flex-1 bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                    <button type="button" onclick="sendTestMail()"
                            class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold px-6 py-2 rounded-lg">
                        ✉️ Test Gönder
                    </button>
                </div>
                <p id="test-mail-result" class="text-xs mt-2"></p>
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
	
	async function sendTestMail() {
    const to = document.getElementById('test-mail-to').value.trim();
    const result = document.getElementById('test-mail-result');

    if (!to) {
        result.className = 'text-xs mt-2 text-red-400';
        result.textContent = 'Lütfen bir e-posta adresi girin.';
        return;
    }

    result.className = 'text-xs mt-2 text-gray-400';
    result.textContent = '⏳ Gönderiliyor...';

    try {
        const form = document.querySelector('form');
        const formData = new FormData(form);
        formData.append('test_mail_to', to);

        const res = await fetch('/public/admin/settings/test-mail', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        });
        const data = await res.json();

        if (data.ok) {
            result.className = 'text-xs mt-2 text-green-400';
            result.textContent = '✅ ' + data.message;
        } else {
            result.className = 'text-xs mt-2 text-red-400';
            result.textContent = '❌ ' + data.error;
        }
    } catch (e) {
        result.className = 'text-xs mt-2 text-red-400';
        result.textContent = '❌ Hata: ' + e.message;
    }
}
});
</script>

