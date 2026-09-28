<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giriş Yap — FtoniNuke</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, system-ui, sans-serif;
            background: linear-gradient(135deg, #2c3e50, #3498db);
            min-height: 100vh; margin: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .login-box {
            background: #fff; padding: 40px;
            border-radius: 12px; width: 100%; max-width: 400px;
            box-shadow: 0 10px 40px rgba(0,0,0,.2);
        }
        h1 { margin: 0 0 8px; color: #2c3e50; text-align: center; }
        .subtitle { text-align: center; color: #7f8c8d; margin-bottom: 24px; }
        label { display: block; margin-bottom: 6px; font-weight: 500; color: #34495e; }
        input {
            width: 100%; padding: 12px;
            border: 1px solid #dcdde1; border-radius: 6px;
            font-size: 15px; margin-bottom: 16px;
        }
        input:focus { outline: none; border-color: #3498db; }
        button {
            width: 100%; padding: 12px;
            background: #2c3e50; color: #fff;
            border: none; border-radius: 6px;
            font-size: 16px; font-weight: 600;
            cursor: pointer; transition: background .2s;
        }
        button:hover { background: #34495e; }
        .error {
            background: #fee; color: #c0392b;
            padding: 12px; border-radius: 6px;
            margin-bottom: 16px; font-size: 14px;
        }
        .back { text-align: center; margin-top: 16px; }
        .back a { color: #7f8c8d; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>🚀 FtoniNuke</h1>
        <p class="subtitle">Yönetim Paneli Girişi</p>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="/public/login">
            <label>Kullanıcı Adı</label>
            <input type="text" name="username" required autofocus>

            <label>Şifre</label>
            <input type="password" name="password" required>

            <button type="submit">Giriş Yap</button>
        </form>

        <div class="back"><a href="/public/">← Ana sayfaya dön</a></div>
    </div>
</body>
</html>