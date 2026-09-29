<?php
/**
 * PHP Built-in Server Router
 * Statik dosyalar doğrudan, diğerleri public/index.php'ye
 */

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$uri = urldecode($uri);

// 1) Statik dosya uzantıları → doğrudan serve
$staticExtensions = ['css','js','png','jpg','jpeg','gif','svg','webp','ico','woff','woff2','ttf','eot','map','json','xml','txt','pdf','zip'];
$ext = strtolower(pathinfo($uri, PATHINFO_EXTENSION));

if (in_array($ext, $staticExtensions, true)) {
    $file = __DIR__ . $uri;
    if (file_exists($file) && is_file($file)) {
        return false; // PHP built-in server kendisi serve eder
    }
}

// 2) /public/ altındaki .php dosyaları (sitemap.php gibi) → doğrudan çalıştır
//    Ama /public/index.php HARİÇ (o zaten ana router)
if (preg_match('#^/public/[a-zA-Z0-9_\-]+\.php$#', $uri) && $uri !== '/public/index.php') {
    $file = __DIR__ . $uri;
    if (file_exists($file) && is_file($file)) {
        require $file;
        return true;
    }
}

// 3) /install/ ve /admin/ gibi dizinler → direkt çalıştır
if (preg_match('#^/(install|admin)/#', $uri)) {
    $file = __DIR__ . $uri;
    if (is_dir($file)) {
        $file = rtrim($file, '/') . '/index.php';
    }
    if (file_exists($file) && is_file($file)) {
        require $file;
        return true;
    }
}

// 4) /uploads/ → doğrudan serve
if (strpos($uri, '/uploads/') === 0 || strpos($uri, '/public/uploads/') === 0) {
    $file = __DIR__ . $uri;
    if (file_exists($file) && is_file($file)) {
        return false;
    }
}

// 5) Diğer her şey → public/index.php (SEO router)
$indexFile = __DIR__ . '/public/index.php';
if (file_exists($indexFile)) {
    require $indexFile;
    return true;
}

http_response_code(404);
echo '404 Not Found';
return true;
