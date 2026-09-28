<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($story['title']) ?> — <?= htmlspecialchars($siteName) ?></title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, system-ui, sans-serif; margin: 0; padding: 0 20px 40px; background: #f0f2f5; color: #222; line-height: 1.7; }
        .container { max-width: 1280px; margin: 0 auto; }

        .layout-grid {
            display: grid;
            grid-template-columns: 240px 1fr 260px;
            gap: 24px;
            margin-top: 24px;
        }
        .col-left, .col-right { align-self: start; position: sticky; top: 100px; }
        .col-main { min-width: 0; }

        .card { background: #fff; padding: 32px; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        h1.article-title { margin: 0 0 20px; color: #2c3e50; font-size: 1.9em; line-height: 1.25; }
        .hero { width: 100%; max-height: 500px; object-fit: cover; border-radius: 10px; margin-bottom: 24px; display: block; }
        .meta { color: #95a5a6; font-size: .9em; padding-bottom: 16px; border-bottom: 1px solid #eee; margin-bottom: 24px; display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
        .meta .cat { background: #3498db; color: #fff; padding: 4px 12px; border-radius: 12px; font-size: .8em; font-weight: 700; text-transform: uppercase; }
        .content { font-size: 1.05em; line-height: 1.8; color: #333; }
        .content img { max-width: 100%; height: auto; border-radius: 8px; margin: 16px 0; }
        .content h1, .content h2, .content h3 { color: #2c3e50; margin-top: 28px; }
        .content blockquote { border-left: 4px solid #3498db; padding-left: 16px; color: #555; font-style: italic; margin: 20px 0; }
        .content ul, .content ol { padding-left: 28px; }
        .content a { color: #2980b9; }

        /* ═══ NAVIGASYON BUTONLARI ═══ */
        .post-nav {
            margin-top: 40px;
            padding-top: 32px;
            border-top: 2px solid #f0f2f5;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .post-nav-item {
            display: flex; align-items: center; gap: 14px;
            padding: 16px 20px;
            background: #f8f9fa;
            border-radius: 10px;
            text-decoration: none;
            color: #333;
            border: 2px solid transparent;
            transition: all .25s;
            min-height: 90px;
            position: relative;
            overflow: hidden;
        }
        .post-nav-item:hover {
            background: #fff;
            border-color: #3498db;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(52,152,219,.15);
        }
        .post-nav-item.prev { text-align: left; }
        .post-nav-item.next { text-align: right; flex-direction: row-reverse; }
        .post-nav-item .nav-thumb {
            width: 64px; height: 64px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            background: #e9ecef;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            color: #adb5bd;
        }
        .post-nav-item .nav-thumb img {
            width: 100%; height: 100%; object-fit: cover;
        }
        .post-nav-item .nav-body { flex: 1; min-width: 0; }
        .post-nav-item .nav-label {
            display: block;
            font-size: .75em;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #3498db;
            margin-bottom: 4px;
        }
        .post-nav-item .nav-title {
            display: block;
            font-size: .95em;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1.35;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .post-nav-item:hover .nav-title { color: #2980b9; }

        /* Ana sayfaya dön butonu */
        .back-home {
            grid-column: 1 / -1;
            text-align: center;
            margin-top: 8px;
        }
        .back-home a {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 24px;
            background: #2c3e50;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: .9em;
            transition: all .2s;
        }
        .back-home a:hover {
            background: #34495e;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(44,62,80,.3);
        }
        .back-home a::before { content: '⌂'; font-size: 1.1em; }

        footer { text-align: center; color: #95a5a6; margin-top: 40px; padding: 20px; font-size: .9em; }

        @media (max-width: 1000px) {
            .layout-grid { grid-template-columns: 1fr; }
            .col-left, .col-right { position: static; }
        }
        @media (max-width: 600px) {
            .card { padding: 20px; }
            h1.article-title { font-size: 1.4em; }
            .post-nav { grid-template-columns: 1fr; }
            .post-nav-item.next { flex-direction: row; text-align: left; }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php require __DIR__ . '/partials/header.html.php'; ?>

        <div class="layout-grid">

            <!-- ⬅️ SOL SÜTUN -->
            <aside class="col-left">
                <?php $position = 'l'; require __DIR__ . '/partials/blocks.html.php'; ?>
            </aside>

            <!-- 📄 ORTA SÜTUN -->
            <main class="col-main">
                <div class="card">
                    <h1 class="article-title"><?= htmlspecialchars($story['title']) ?></h1>

                    <?php if (!empty($story['image'])): ?>
                        <img src="<?= htmlspecialchars($story['image']) ?>"
                             alt="<?= htmlspecialchars($story['title']) ?>"
                             class="hero">
                    <?php endif; ?>

                    <div class="meta">
                        <?php if (!empty($story['topic_name'])): ?>
                            <span class="cat"><?= htmlspecialchars($story['topic_name']) ?></span>
                        <?php endif; ?>
                        <span>👤 <?= htmlspecialchars($story['author_name'] ?? $story['aid'] ?? 'Bilinmeyen') ?></span>
                        <span>📅 <?= date('d.m.Y H:i', (int)$story['time']) ?></span>
                        <?php if (!empty($story['informant'])): ?>
                            <span>📍 <?= htmlspecialchars($story['informant']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="content">
                        <?= $story['bodytext'] ?: htmlspecialchars($story['hometext'] ?? '') ?>
                    </div>

                    <!-- ═══ NAVIGASYON ═══ -->
                    <nav class="post-nav">
                        <?php if (!empty($prevStory)): ?>
                            <a href="/public/?sid=<?= (int)$prevStory['sid'] ?>" class="post-nav-item prev">
                                <div class="nav-thumb">
                                    <?php if (!empty($prevStory['image'])): ?>
                                        <img src="<?= htmlspecialchars($prevStory['image']) ?>" alt="">
                                    <?php else: ?>
                                        📰
                                    <?php endif; ?>
                                </div>
                                <div class="nav-body">
                                    <span class="nav-label">← Önceki Haber</span>
                                    <span class="nav-title"><?= htmlspecialchars($prevStory['title']) ?></span>
                                </div>
                            </a>
                        <?php else: ?>
                            <div></div>
                        <?php endif; ?>

                        <?php if (!empty($nextStory)): ?>
                            <a href="/public/?sid=<?= (int)$nextStory['sid'] ?>" class="post-nav-item next">
                                <div class="nav-thumb">
                                    <?php if (!empty($nextStory['image'])): ?>
                                        <img src="<?= htmlspecialchars($nextStory['image']) ?>" alt="">
                                    <?php else: ?>
                                        📰
                                    <?php endif; ?>
                                </div>
                                <div class="nav-body">
                                    <span class="nav-label">Sonraki Haber →</span>
                                    <span class="nav-title"><?= htmlspecialchars($nextStory['title']) ?></span>
                                </div>
                            </a>
                        <?php else: ?>
                            <div></div>
                        <?php endif; ?>

                        <div class="back-home">
                            <a href="/public/">Ana Sayfaya Dön</a>
                        </div>
                    </nav>
                </div>
            </main>

            <!-- ➡️ SAĞ SÜTUN -->
            <aside class="col-right">
                <?php $position = 'r'; require __DIR__ . '/partials/blocks.html.php'; ?>
            </aside>
        </div>

        <footer>FtoniNuke &copy; <?= date('Y') ?> — Modern PHP-Nuke</footer>
    </div>

    <?php require __DIR__ . '/partials/blocks-styles.html.php'; ?>
</body>
</html>
