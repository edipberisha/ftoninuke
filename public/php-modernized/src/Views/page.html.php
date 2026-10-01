<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($page['title']) ?> — <?= htmlspecialchars($siteName) ?></title>
    <?php if (!empty($page['meta_description'])): ?>
        <meta name="description" content="<?= htmlspecialchars($page['meta_description']) ?>">
    <?php endif; ?>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, system-ui, sans-serif; margin: 0; padding: 0 20px 40px; background: #f0f2f5; color: #222; line-height: 1.7; }
        .container { max-width: 1280px; margin: 0 auto; }
        .layout-grid { display: grid; grid-template-columns: 240px 1fr 260px; gap: 24px; margin-top: 24px; }
        .col-left, .col-right { align-self: start; position: sticky; top: 100px; }
        .col-main { min-width: 0; }
        .card { background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .page-title { margin: 0 0 24px; color: #2c3e50; font-size: 2em; border-bottom: 3px solid #3498db; padding-bottom: 16px; }
        .page-content { font-size: 1.05em; }
        .page-content h2 { color: #2c3e50; margin-top: 32px; }
        .page-content h3 { color: #34495e; }
        .page-content img { max-width: 100%; height: auto; border-radius: 8px; }
        .page-content a { color: #2980b9; }
        .page-content blockquote { border-left: 4px solid #3498db; padding-left: 16px; color: #555; font-style: italic; }
        footer { text-align: center; color: #95a5a6; margin-top: 40px; padding: 20px; font-size: .9em; }
        @media (max-width: 1000px) { .layout-grid { grid-template-columns: 1fr; } .col-left, .col-right { position: static; } }
        @media (max-width: 600px) { .card { padding: 24px; } .page-title { font-size: 1.5em; } }
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
                    <h1 class="page-title"><?= htmlspecialchars($page['title']) ?></h1>
                    <div class="page-content">
                        <?= $page['content'] ?>
                    </div>
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
