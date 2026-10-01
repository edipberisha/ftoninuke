<?php
$flash = $_SESSION['contact_flash'] ?? null;
unset($_SESSION['contact_flash']);
$old = $_SESSION['contact_old'] ?? [];
unset($_SESSION['contact_old']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($page['title']) ?> — <?= htmlspecialchars($siteName) ?></title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, system-ui, sans-serif; margin: 0; padding: 0 20px 40px; background: #f0f2f5; color: #222; line-height: 1.7; }
        .container { max-width: 1280px; margin: 0 auto; }
        .layout-grid { display: grid; grid-template-columns: 240px 1fr 260px; gap: 24px; margin-top: 24px; }
        .col-left, .col-right { align-self: start; position: sticky; top: 100px; }
        .col-main { min-width: 0; }
        .card { background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .page-title { margin: 0 0 24px; color: #2c3e50; font-size: 2em; border-bottom: 3px solid #3498db; padding-bottom: 16px; }
        .contact-intro { margin-bottom: 32px; color: #555; }
        .contact-form { display: grid; gap: 16px; }
        .cf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; color: #34495e; font-size: .9em; }
        input, textarea { width: 100%; padding: 12px 14px; border: 1px solid #dcdde1; border-radius: 6px; font-family: inherit; font-size: 15px; background: #fff; }
        input:focus, textarea:focus { outline: none; border-color: #3498db; box-shadow: 0 0 0 3px rgba(52,152,219,.15); }
        textarea { min-height: 160px; resize: vertical; }
        button { background: #2c3e50; color: #fff; padding: 14px 40px; border: none; border-radius: 6px; font-weight: 700; font-size: 1em; cursor: pointer; transition: all .2s; }
        button:hover { background: #34495e; transform: translateY(-1px); }
        .flash { padding: 16px 20px; border-radius: 8px; margin-bottom: 24px; font-size: .95em; }
        .flash.success { background: #d4edda; color: #155724; border-left: 4px solid #27ae60; }
        .flash.error { background: #f8d7da; color: #721c24; border-left: 4px solid #e74c3c; }
        .honeypot { position: absolute; left: -9999px; }
        .contact-info { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 32px; }
        .contact-info-item { display: flex; align-items: center; gap: 12px; padding: 16px; background: #f8f9fa; border-radius: 8px; font-size: .9em; }
        .contact-info-item .icon { font-size: 1.4em; }
        footer { text-align: center; color: #95a5a6; margin-top: 40px; padding: 20px; font-size: .9em; }
        @media (max-width: 1000px) { .layout-grid { grid-template-columns: 1fr; } .col-left, .col-right { position: static; } }
        @media (max-width: 600px) { .card { padding: 24px; } .page-title { font-size: 1.5em; } .cf-row, .contact-info { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <?php require __DIR__ . '/partials/header.html.php'; ?>

        <div class="layout-grid">
            <aside class="col-left">
                <?php $position = 'l'; require __DIR__ . '/partials/blocks.html.php'; ?>
            </aside>

            <main class="col-main">
                <div class="card">
                    <h1 class="page-title">📬 <?= htmlspecialchars($page['title']) ?></h1>

                    <?php if (!empty($page['content'])): ?>
                        <div class="contact-intro"><?= $page['content'] ?></div>
                    <?php endif; ?>

                    <div class="contact-info">
                        <div class="contact-info-item">
                            <span class="icon">✉️</span>
                            <div>
                                <strong>E-posta</strong><br>
                                <small>Formu doldurun, en kısa sürede yanıt verelim</small>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <span class="icon">⏱️</span>
                            <div>
                                <strong>Yanıt Süresi</strong><br>
                                <small>Genellikle 24 saat içinde</small>
                            </div>
                        </div>
                    </div>

                    <?php if ($flash): ?>
                        <div class="flash <?= $flash['type'] ?>">
                            <?= htmlspecialchars($flash['msg']) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="/public/contact/submit" class="contact-form">
                        <input type="text" name="website_url" class="honeypot" tabindex="-1" autocomplete="off">

                        <div class="cf-row">
                            <div>
                                <label>Adınız *</label>
                                <input type="text" name="name" required minlength="2"
                                       value="<?= htmlspecialchars($old['name'] ?? '') ?>">
                            </div>
                            <div>
                                <label>E-posta *</label>
                                <input type="email" name="email" required
                                       value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                            </div>
                        </div>

                        <div>
                            <label>Konu</label>
                            <input type="text" name="subject"
                                   value="<?= htmlspecialchars($old['subject'] ?? '') ?>">
                        </div>

                        <div>
                            <label>Mesajınız *</label>
                            <textarea name="message" required minlength="10"><?= htmlspecialchars($old['message'] ?? '') ?></textarea>
                        </div>

                        <div>
                            <button type="submit">📨 Mesajı Gönder</button>
                        </div>
                    </form>
                </div>
            </main>

            <aside class="col-right">
                <?php $position = 'r'; require __DIR__ . '/partials/blocks.html.php'; ?>
            </aside>
        </div>

        <footer>FtoniNuke &copy; <?= date('Y') ?> — Modern PHP-Nuke</footer>
    </div>

    <?php require __DIR__ . '/partials/blocks-styles.html.php'; ?>
</body>
</html>
