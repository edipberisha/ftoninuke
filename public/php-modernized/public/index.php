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

// ─── Admin ─────────────────────────────────────────
$router->get('/admin',           [AdminController::class, 'dashboard']);
$router->get('/admin/media',     [AdminController::class, 'media']);
$router->get('/admin/themes',    [AdminController::class, 'themes']);
$router->get('/admin/widgets',   [AdminController::class, 'widgets']);
$router->get('/admin/users',     [AdminController::class, 'users']);
$router->get('/admin/security',  [AdminController::class, 'security']);
$router->get('/admin/blocks',    [AdminController::class, 'blocks']);
$router->get('/admin/topics',    [AdminController::class, 'topics']);
$router->get('/admin/menu', [AdminController::class, 'menu']);
$router->post('/admin/menu', [AdminController::class, 'menu']);
$router->get('/admin/settings',  [AdminController::class, 'settings']);

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
// ─── İsteği işle ────────────────────────────────────
$router->dispatch();