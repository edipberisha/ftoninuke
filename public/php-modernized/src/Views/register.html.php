<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kayıt Ol — FtoniNuke</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, system-ui, sans-serif; background: linear-gradient(135deg, #2c3e50, #3498db); min-height: 100vh; margin: 0; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .box { background: #fff; padding: 40px; border-radius: 12px; width: 100%; max-width: 440px; box-shadow: 0 10px 40px rgba(0,0,0,.2); }
        h1 { margin: 0 0 8px; color: #2c3e50; text-align: center; }
        .subtitle { text-align: center; color: #7f8c8d; margin-bottom: 24px; font-size: .9em; }
        label { display: block; margin-bottom: 6px; font-weight: 500; color: #34495e; font-size: .9em; }
        input { width: 100%; padding: 12px; border: 1px solid #dcdde1; border-radius: 6px; font-size: 15px; margin-bottom: 16px; }
        input:focus { outline: none; border-color: #3498db; }
        button { width: 100%; padding: 12px; background: #2c3e50; color: #fff; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background .2s; }
        button:hover { background: #34495e; }
        .error { background: #fee; color: #c0392b; padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; }
        .back { text-align: center; margin-top: 16px; font-size: 13px; }
        .back a { color: #7f8c8d; text-decoration: none; margin: 0 8px; }
        .honeypot { position: absolute; left: -9999px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>🚀 Kayıt Ol</h1>
        <p class="subtitle"><?= htmlspecialchars($siteName ?? 'FtoniNuke') ?></p>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="/public/register">
            <input type="text" name="website_url" class="honeypot" tabindex="-1" autocomplete="off">

            <label>Kullanıcı Adı</label>
            <input type="text" name="username" required minlength="3"
                   value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                   placeholder="ahmet_yilmaz">

            <label>E-posta</label>
            <input type="email" name="user_email" required
                   value="<?= htmlspecialchars($old['user_email'] ?? '') ?>"
                   placeholder="ornek@mail.com">

            <label>Şifre</label>
            <input type="password" name="password" required minlength="6"
                   placeholder="En az 6 karakter">

            <label>Şifre (Tekrar)</label>
            <input type="password" name="password_confirm" required minlength="6"
                   placeholder="Şifreyi tekrarla">

            <button type="submit">Hesap Oluştur</button>
        </form>

        <div class="back">
            <a href="/public/login">Zaten üyeyim →</a>
            <a href="/public/">← Ana sayfa</a>
        </div>
    </div>
</body>
</html>
