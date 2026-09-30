<?php
// Menü öğelerini çek (cache'lenebilir)
if (!isset($menuItems)) {
    $menuRepo = new \FtoniNuke\Repository\MenuRepository();
    $menuItems = $menuRepo->allHierarchical();
}
if (!isset($siteName)) {
    $siteName = 'FtoniNuke';
}
$currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
?>

<style>
    .site-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #e2e8f0;
        box-shadow: 0 4px 24px rgba(0,0,0,.15);
        position: sticky; top: 0; z-index: 100;
        border-radius: 12px;
        margin: 20px auto 24px;
        max-width: 1000px;
    }
    .header-inner {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 24px; gap: 24px;
    }
    .site-logo {
        display: flex; align-items: center; gap: 10px;
        text-decoration: none; color: #fff;
        font-weight: 900; font-size: 1.2em;
        letter-spacing: -.3px;
        transition: transform .2s;
        white-space: nowrap;
    }
    .site-logo:hover { transform: scale(1.03); }
    .site-logo .logo-icon { font-size: 1.5em; }
    .site-logo .logo-text {
        background: linear-gradient(135deg, #60a5fa, #a78bfa);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .main-nav { flex: 1; }
    .main-nav ul {
        list-style: none; margin: 0; padding: 0;
        display: flex; gap: 4px; align-items: center;
        justify-content: flex-end;
    }
    .main-nav > ul > li { position: relative; }
    .main-nav a {
        display: flex; align-items: center; gap: 6px;
        color: #cbd5e1; text-decoration: none;
        padding: 9px 14px; border-radius: 8px;
        font-weight: 500; font-size: .93em;
        transition: all .2s;
        position: relative;
        white-space: nowrap;
    }
    .main-nav > ul > li > a::before {
        content: ''; position: absolute;
        left: 14px; right: 14px; bottom: 4px;
        height: 2px; background: linear-gradient(90deg, #3b82f6, #a78bfa);
        transform: scaleX(0); transform-origin: left;
        transition: transform .25s;
        border-radius: 2px;
    }
    .main-nav > ul > li > a:hover::before,
    .main-nav > ul > li > a.active::before { transform: scaleX(1); }
    .main-nav > ul > li > a:hover {
        color: #fff; background: rgba(59,130,246,.08);
    }
    .main-nav > ul > li > a.active { color: #fff; }
    .main-nav > ul > li > a.active::after {
        content: ''; position: absolute;
        top: 50%; left: 0; transform: translateY(-50%);
        width: 3px; height: 60%; background: #3b82f6;
        border-radius: 0 3px 3px 0;
    }

    /* Dropdown */
    .has-dropdown > a::after {
        content: '▾'; margin-left: 4px; font-size: .8em; transition: transform .2s;
    }
    .has-dropdown:hover > a::after { transform: rotate(180deg); }
    .dropdown {
        position: absolute; top: 100%; left: 0;
        background: #1e293b;
        border: 1px solid #334155;
        border-radius: 10px;
        padding: 6px; min-width: 200px;
        box-shadow: 0 12px 32px rgba(0,0,0,.4);
        opacity: 0; visibility: hidden;
        transform: translateY(-8px);
        transition: all .2s;
        list-style: none; margin: 0;
        display: flex; flex-direction: column; gap: 2px;
        z-index: 200;
    }
    .has-dropdown:hover .dropdown {
        opacity: 1; visibility: visible; transform: translateY(4px);
    }
    .dropdown li { position: relative; }
    .dropdown a {
        padding: 9px 12px; border-radius: 6px;
        font-size: .88em; color: #cbd5e1;
    }
    .dropdown a::before { display: none; }
    .dropdown a:hover {
        background: rgba(59,130,246,.15); color: #fff;
    }

    /* Mobil */
    .mobile-toggle {
        display: none; background: transparent; border: none;
        color: #e2e8f0; font-size: 1.5em; cursor: pointer;
        padding: 4px 8px;
    }
    @media (max-width: 768px) {
        .header-inner { padding: 12px 16px; }
        .site-logo { font-size: 1em; }
        .mobile-toggle { display: block; }
        .main-nav {
            position: absolute; top: 100%; left: 0; right: 0;
            background: #0f172a; border-radius: 0 0 12px 12px;
            padding: 12px; display: none; margin-top: 8px;
            box-shadow: 0 12px 32px rgba(0,0,0,.5);
        }
        .main-nav.open { display: block; }
        .main-nav ul { flex-direction: column; align-items: stretch; gap: 4px; }
        .main-nav a { justify-content: space-between; padding: 12px 14px; }
        .dropdown {
            position: static; opacity: 1; visibility: visible;
            transform: none; margin-left: 12px; box-shadow: none;
            border: none; background: transparent; padding: 0;
        }
        .dropdown a { font-size: .85em; }
    }
</style>

<header class="site-header">
    <div class="header-inner">
        <a href="/public/" class="site-logo">
            <span class="logo-icon">🚀</span>
            <span class="logo-text"><?= htmlspecialchars($siteName) ?></span>
        </a>

        <nav class="main-nav" id="main-nav">
            <ul>
                <?php foreach ($menuItems as $item): ?>
                    <?php
                        $itemUrl = $item['url'] ?? '/';
                        $isActive = (rtrim($currentUrl, '/') === rtrim($itemUrl, '/'))
                                  || ($itemUrl !== '/public/' && strpos($currentUrl, $itemUrl) === 0);
                        $hasChildren = !empty($item['children']);
                    ?>
                    <li class="<?= $hasChildren ? 'has-dropdown' : '' ?>">
                        <a href="<?= htmlspecialchars($itemUrl) ?>"
                           target="<?= htmlspecialchars($item['target'] ?? '_self') ?>"
                           class="<?= $isActive ? 'active' : '' ?>">
                            <?= htmlspecialchars($item['title']) ?>
                        </a>
                        <?php if ($hasChildren): ?>
                            <ul class="dropdown">
                                <?php foreach ($item['children'] as $child): ?>
                                    <li>
                                        <a href="<?= htmlspecialchars($child['url']) ?>"
                                           target="<?= htmlspecialchars($child['target'] ?? '_self') ?>">
                                            <?= htmlspecialchars($child['title']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
			
        </nav>
<?php
$settings = new \FtoniNuke\Service\SettingsService();
$isLoggedIn = !empty($_SESSION['user_id']);
?>
<?php if (!$isLoggedIn): ?>
    <li>
        <a href="/public/login" style="color: #60a5fa;">
            🔑 Giriş
        </a>
    </li>
    <?php if ($settings->isRegistrationEnabled()): ?>
        <li>
            <a href="/public/register" style="color: #10b981;">
                ✨ Kayıt Ol
            </a>
        </li>
    <?php endif; ?>
<?php else: ?>
    <li>
        <a href="/public/admin" style="color: #60a5fa;">
            👤 <?= htmlspecialchars($_SESSION['username'] ?? '') ?>
        </a>
    </li>
<?php endif; ?>
        <button class="mobile-toggle" onclick="document.getElementById('main-nav').classList.toggle('open')">☰</button>
    </div>
</header>
