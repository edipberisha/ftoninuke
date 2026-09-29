<?php
/**
 * PHP Built-in Server Router
 *
 * Kullanım:
 *   php -S 0.0.0.0:8000 router.php
 *
 * Ne yapar:
 *   - Statik dosyalar (CSS, JS, PNG, uploads/) doğrudan serve edilir
 *   - Diğer tüm istekler public/index.php'ye yönlendirilir (SEO URL desteği)
 */

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

// 1) Statik dosyalar — doğrudan serve et
$staticExtensions = ['css','js','png','jpg','jpeg','gif','svg','webp','ico','woff','woff2','ttf','eot','map','json','xml','txt','pdf','zip'];
$ext = strtolower(pathinfo($uri, PATHINFO_EXTENSION));

if (in_array($ext, $staticExtensions, true)) {
    $file = __DIR__ . $uri;
    if (file_exists($file) && is_file($file)) {
        return false; // PHP built-in server kendisi serve eder
    }
}

// 2) /install/ ve /admin/ gibi dizinler — direkt çalıştır
if (preg_match('#^/(install|admin)/#', $uri)) {
    $file = __DIR__ . $uri;
    // Dizin ise index.php'yi dene
    if (is_dir($file)) {
        $file = rtrim($file, '/') . '/index.php';
    }
    if (file_exists($file) && is_file($file)) {
        require $file;
        return true;
    }
}

// 3) /uploads/ — doğrudan serve
if (strpos($uri, '/uploads/') === 0 || strpos($uri, '/public/uploads/') === 0) {
    $file = __DIR__ . $uri;
    if (file_exists($file) && is_file($file)) {
        return false;
    }
}

// 4) Diğer her şey → public/index.php
$indexFile = __DIR__ . '/public/index.php';
if (file_exists($indexFile)) {
    require $indexFile;
    return true;
}

// 5) Fallback — bulunamadı
http_response_code(404);
echo '404 Not Found';
return true;
