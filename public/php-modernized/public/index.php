<?php

declare(strict_types=1);

/**
 * FtoniNuke v1.4 - Front Controller
 */

$lockFile = __DIR__ . '/../install/installed.lock';

if (!file_exists($lockFile)) {
    header('Location: /install/index.php');
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

use FtoniNuke\Core\Bootstrap;
use FtoniNuke\Core\Router;
use FtoniNuke\Controller\HomeController;
use FtoniNuke\Controller\AuthController;
use FtoniNuke\Controller\AdminController;
use FtoniNuke\Controller\Api\UploadController;
use FtoniNuke\Controller\CommentController;
use FtoniNuke\Helper\SlugHelper;

// Sistemi başlat
Bootstrap::run();

// Router'ı kur
$router = new Router();

// ─── Public ────────────────────────────────────────
$router->get('/', [HomeController::class, 'index']);

// ─── Auth ──────────────────────────────────────────
$router->get('/login',  [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/register',  [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);

// ─── Admin ─────────────────────────────────────────
$router->get('/admin',           [AdminController::class, 'dashboard']);
$router->get('/admin/media',     [AdminController::class, 'media']);
$router->get('/admin/themes',    [AdminController::class, 'themes']);
$router->get('/admin/widgets',   [AdminController::class, 'widgets']);
$router->get('/admin/users',     [AdminController::class, 'users']);
$router->get('/admin/users/create',  [AdminController::class, 'userCreate']);
$router->post('/admin/users/create', [AdminController::class, 'userCreate']);
$router->get('/admin/users/edit',    [AdminController::class, 'userEdit']);
$router->post('/admin/users/edit',   [AdminController::class, 'userEdit']);
$router->get('/admin/users/delete',  [AdminController::class, 'userDelete']);
$router->get('/admin/security',  [AdminController::class, 'security']);
$router->get('/admin/blocks',    [AdminController::class, 'blocks']);
$router->get('/admin/comments', [AdminController::class, 'comments']);
$router->get('/admin/topics',    [AdminController::class, 'topics']);
$router->post('/admin/topics',   [AdminController::class, 'topics']);
$router->get('/admin/menu', [AdminController::class, 'menu']);
$router->post('/admin/menu', [AdminController::class, 'menu']);
$router->get('/admin/settings',  [AdminController::class, 'settings']);
$router->post('/admin/settings', [AdminController::class, 'settings']);
$router->post('/admin/settings/test-mail', [AdminController::class, 'settingsTestMail']);
// Pages CRUD
$router->get('/admin/pages',         [AdminController::class, 'pages']);
$router->get('/admin/pages/create',  [AdminController::class, 'pageCreate']);
$router->post('/admin/pages/create', [AdminController::class, 'pageCreate']);
$router->get('/admin/pages/edit',    [AdminController::class, 'pageEdit']);
$router->post('/admin/pages/edit',   [AdminController::class, 'pageEdit']);
$router->get('/admin/pages/delete',  [AdminController::class, 'pageDelete']);
$router->get('/admin/pages/toggle',  [AdminController::class, 'pageToggle']);

// Messages
$router->get('/admin/messages', [AdminController::class, 'messages']);

// Import
$router->get('/admin/import',        [AdminController::class, 'import']);
$router->post('/admin/import/run',   [AdminController::class, 'importRun']);
$router->get('/admin/import/report', [AdminController::class, 'importReport']);

// ─── News CRUD (admin) ─────────────────────────────
$router->get('/admin/news',         [AdminController::class, 'news']);
$router->get('/admin/news/create',  [AdminController::class, 'newsCreate']);
$router->post('/admin/news/create', [AdminController::class, 'newsCreate']);
$router->get('/admin/news/edit',    [AdminController::class, 'newsEdit']);
$router->post('/admin/news/edit',   [AdminController::class, 'newsEdit']);
$router->get('/admin/news/delete',  [AdminController::class, 'newsDelete']);

// Blocks CRUD
$router->get('/admin/blocks',          [AdminController::class, 'blocks']);
$router->get('/admin/blocks/create',   [AdminController::class, 'blockCreate']);
$router->post('/admin/blocks/create',  [AdminController::class, 'blockCreate']);
$router->get('/admin/blocks/edit',     [AdminController::class, 'blockEdit']);
$router->post('/admin/blocks/edit',    [AdminController::class, 'blockEdit']);
$router->get('/admin/blocks/delete',   [AdminController::class, 'blockDelete']);
$router->get('/admin/blocks/toggle',   [AdminController::class, 'blockToggle']);

// ─── Frontend: Haber detay (?sid=X) ─────────────────
// Router'dan önce kontrol et, çünkü '/' route'u da bu query string ile gelebilir
if (isset($_GET['sid']) && is_numeric($_GET['sid']) && strpos($_SERVER['REQUEST_URI'] ?? '', '/admin') === false) {
    (new HomeController())->show((int)$_GET['sid']);
    exit;
}

// ─── API ────────────────────────────────────────────
$router->post('/api/upload', [UploadController::class, 'image']);
$router->get('/api/media', [\FtoniNuke\Controller\Api\MediaController::class, 'list']);

// ─── Yorumlar ────────────────────────────────────────
$router->post('/comment/submit',    [CommentController::class, 'submit']);
$router->get('/comment/captcha',    [CommentController::class, 'refreshCaptcha']);

// ─── SEO URL — Sayfa / Haber Detay (slug ile) ────
$router->get('/{slug}', function(string $slug) {
    $slug = preg_replace('/\.html?$/', '', $slug);

    // Önce statik sayfa mı?
    $pageRepo = new \FtoniNuke\Repository\PageRepository();
    if ($pageRepo->findBySlug($slug)) {
        (new \FtoniNuke\Controller\PageController())->show($slug);
        return;
    }

    // Değilse haber
    (new HomeController())->showBySlug($slug);
});

// ─── İletişim formu POST ─────────────────────────
$router->post('/contact/submit', [\FtoniNuke\Controller\PageController::class, 'contactSubmit']);

// ─── İsteği işle ────────────────────────────────────
$router->dispatch();