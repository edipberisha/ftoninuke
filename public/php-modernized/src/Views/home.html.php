<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($siteName) ?></title>
    <meta name="description" content="<?= htmlspecialchars($siteName) ?> - Son haberler ve güncel içerikler">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, system-ui, sans-serif; margin: 0; padding: 0 20px 40px; background: #f0f2f5; color: #222; }
        .container { max-width: 1280px; margin: 0 auto; }

        .layout-grid {
            display: grid;
            grid-template-columns: 240px 1fr 260px;
            gap: 24px;
            margin-top: 24px;
        }
        .col-left, .col-right { align-self: start; position: sticky; top: 100px; }
        .col-main { min-width: 0; }

        /* Slider */
        .hero-slider { border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.12); margin-bottom: 24px; }
        .swiper { width: 100%; height: 400px; }
        .swiper-slide { position: relative; overflow: hidden; }
        .swiper-slide img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .swiper-slide .overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,.85) 0%, rgba(0,0,0,.4) 50%, transparent 100%);
            display: flex; flex-direction: column; justify-content: flex-end;
            padding: 32px;
            color: #fff;
        }
        .swiper-slide .overlay .cat-tag {
            display: inline-block; align-self: flex-start;
            background: #3498db; color: #fff;
            padding: 4px 12px; border-radius: 12px;
            font-size: .7em; font-weight: 800;
            text-transform: uppercase; letter-spacing: .5px;
            margin-bottom: 12px;
        }
        .swiper-slide .overlay h2 {
            margin: 0 0 8px; font-size: 1.6em; line-height: 1.2;
            text-shadow: 0 2px 8px rgba(0,0,0,.5);
        }
        .swiper-slide .overlay h2 a { color: #fff; text-decoration: none; }
        .swiper-slide .overlay h2 a:hover { text-decoration: underline; }
        .swiper-slide .overlay .meta { font-size: .85em; opacity: .85; }
        .swiper-button-next, .swiper-button-prev { color: #fff !important; text-shadow: 0 2px 6px rgba(0,0,0,.5); }
        .swiper-pagination-bullet { background: #fff !important; opacity: .5; }
        .swiper-pagination-bullet-active { opacity: 1; }

        /* Haber kartları */
        .card { background: #fff; padding: 24px; border-radius: 10px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .card h2 { margin-top: 0; color: #2c3e50; }
        .news-card { display: flex; gap: 20px; padding: 20px 0; border-bottom: 1px solid #eee; }
        .news-card:last-child { border-bottom: none; padding-bottom: 0; }
        .news-image { width: 200px; height: 140px; flex-shrink: 0; border-radius: 8px; overflow: hidden; background: #ecf0f1; }
        .news-image img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .news-image.empty { display: flex; align-items: center; justify-content: center; color: #bdc3c7; font-size: 40px; }
        .news-body { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .news-body a.title { color: #2980b9; font-size: 1.15em; font-weight: 700; text-decoration: none; }
        .news-body a.title:hover { text-decoration: underline; }
        .news-meta { color: #95a5a6; font-size: .85em; margin: 8px 0; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .news-meta .cat { background: #3498db; color: #fff; padding: 3px 10px; border-radius: 12px; font-size: .7em; font-weight: 700; text-transform: uppercase; }
        .news-summary { color: #555; line-height: 1.6; margin-bottom: 12px; flex: 1; }
        .read-more {
            display: inline-block; align-self: flex-start;
            background: #2980b9; color: #fff;
            padding: 8px 18px; border-radius: 6px;
            text-decoration: none; font-weight: 600; font-size: .9em;
            transition: background .2s, transform .1s;
        }
        .read-more:hover { background: #21618c; transform: translateX(2px); }
        .read-more::after { content: ' →'; }

        footer { text-align: center; color: #95a5a6; margin-top: 40px; padding: 20px; font-size: .9em; }

        @media (max-width: 1000px) {
            .layout-grid { grid-template-columns: 1fr; }
            .col-left, .col-right { position: static; }
        }
        @media (max-width: 600px) {
            .swiper { height: 280px; }
            .swiper-slide .overlay { padding: 20px; }
            .swiper-slide .overlay h2 { font-size: 1.1em; }
            .news-card { flex-direction: column; }
            .news-image { width: 100%; height: 180px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php require __DIR__ . '/partials/header.html.php'; ?>

        <?php
        // SEO URL helper — slug varsa slug, yoksa sid
        $newsUrl = function(array $item): string {
            if (!empty($item['slug'])) {
                return '/public/' . htmlspecialchars($item['slug']);
            }
            return '/public/?sid=' . (int)$item['sid'];
        };
        ?>

        <div class="layout-grid">

            <!-- ⬅️ SOL SÜTUN -->
            <aside class="col-left">
                <?php $position = 'l'; require __DIR__ . '/partials/blocks.html.php'; ?>
            </aside>

            <!-- 📄 ORTA SÜTUN -->
            <main class="col-main">
                <?php if (!empty($news)): ?>
                    <?php $featured = array_slice($news, 0, 5); ?>

                    <div class="hero-slider">
                        <div class="swiper">
                            <div class="swiper-wrapper">
                                <?php foreach ($featured as $item): ?>
                                    <div class="swiper-slide">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                                        <?php else: ?>
                                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='1200' height='600'%3E%3Crect fill='%232c3e50' width='1200' height='600'/%3E%3Ctext x='600' y='300' font-size='120' text-anchor='middle' fill='%2334495e'%3E📰%3C/text%3E%3C/svg%3E" alt="">
                                        <?php endif; ?>
                                        <div class="overlay">
                                            <?php if (!empty($item['topic_name'])): ?>
                                                <span class="cat-tag"><?= htmlspecialchars($item['topic_name']) ?></span>
                                            <?php endif; ?>
                                            <h2><a href="<?= $newsUrl($item) ?>"><?= htmlspecialchars($item['title']) ?></a></h2>
                                            <div class="meta">
                                                👤 <?= htmlspecialchars($item['author_name'] ?? 'Bilinmeyen') ?>
                                                &nbsp;•&nbsp;
                                                📅 <?= date('d.m.Y', (int)$item['time']) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>

                    <div class="card">
                        <h2>📰 Son Haberler</h2>
                        <?php foreach ($news as $item): ?>
                            <div class="news-card">
                                <?php if (!empty($item['image'])): ?>
                                    <div class="news-image">
                                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" loading="lazy">
                                    </div>
                                <?php else: ?>
                                    <div class="news-image empty">📰</div>
                                <?php endif; ?>
                                <div class="news-body">
                                    <a class="title" href="<?= $newsUrl($item) ?>">
                                        <?= htmlspecialchars($item['title']) ?>
                                    </a>
                                    <div class="news-meta">
                                        <?php if (!empty($item['topic_name'])): ?>
                                            <span class="cat"><?= htmlspecialchars($item['topic_name']) ?></span>
                                        <?php endif; ?>
                                        <span>👤 <?= htmlspecialchars($item['author_name'] ?? 'Bilinmeyen') ?></span>
                                        <span>📅 <?= date('d.m.Y H:i', (int)$item['time']) ?></span>
                                    </div>
                                    <div class="news-summary">
                                        <?= htmlspecialchars(mb_substr(strip_tags($item['hometext'] ?? ''), 0, 200)) ?>…
                                    </div>
                                    <a class="read-more" href="<?= $newsUrl($item) ?>">Devamını Oku</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <h2>Kurulum Başarılı ✓</h2>
                        <p>Henüz haber yok. <a href="/public/admin/news/create">İlk haberi ekle</a>.</p>
                    </div>
                <?php endif; ?>
            </main>

            <!-- ➡️ SAĞ SÜTUN -->
            <aside class="col-right">
                <?php $position = 'r'; require __DIR__ . '/partials/blocks.html.php'; ?>
            </aside>
        </div>

        <footer>FtoniNuke &copy; <?= date('Y') ?> — Modern PHP-Nuke</footer>
    </div>

    <?php require __DIR__ . '/partials/blocks-styles.html.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
    if (document.querySelector('.swiper')) {
        new Swiper('.swiper', {
            loop: true,
            autoplay: { delay: 5000, disableOnInteraction: false, pauseOnMouseEnter: true },
            speed: 800,
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
            keyboard: { enabled: true },
        });
    }
    </script>
</body>
</html>
