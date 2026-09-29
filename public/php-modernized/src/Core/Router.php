<?php

declare(strict_types=1);

namespace FtoniNuke\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array|callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array|callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        // /public prefix'ini temizle
        $uri = preg_replace('#^/public#', '', $uri) ?: '/';
        $uri = rtrim($uri, '/') ?: '/';

        // 1) Statik route kontrolü
        if (isset($this->routes[$method][$uri])) {
            $this->invoke($this->routes[$method][$uri], []);
            return;
        }

        // 2) Dinamik route kontrolü (örn: /{slug})
        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            if (strpos($pattern, '{') === false) continue;

            $regex = $this->patternToRegex($pattern);
            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches); // tam eşleşmeyi kaldır
                $this->invoke($handler, $matches);
                return;
            }
        }

        // 3) 404
        http_response_code(404);
        echo '<h1>404 Not Found</h1>';
        echo '<p>' . htmlspecialchars($method . ' ' . $uri) . '</p>';
        echo '<p><a href="/public/">Ana sayfaya dön</a></p>';
    }

    /**
     * {slug} pattern'ini regex'e çevir
     * /{slug}          → /([^/]+)
     * /haber/{slug}    → /haber/([^/]+)
     * /{slug}.html     → /([^/.]+)\.html
     */
    private function patternToRegex(string $pattern): string
    {
        $regex = preg_quote($pattern, '#');
        // \{slug\} → ([^/]+)
        $regex = preg_replace('#\\\\\{[a-zA-Z_]+\\\\\}#', '([^/]+)', $regex);
        // Kaçan noktaları geri al (uzantı için)
        $regex = str_replace('\\.', '\\.', $regex);
        return '#^' . $regex . '$#';
    }

    private function invoke(array|callable $handler, array $params): void
    {
        if (is_array($handler)) {
            [$class, $action] = $handler;
            (new $class())->$action(...$params);
        } else {
            $handler(...$params);
        }
    }
}
