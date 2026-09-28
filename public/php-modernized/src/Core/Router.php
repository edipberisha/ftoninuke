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

        $handler = $this->routes[$method][$uri] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo '<h1>404 Not Found</h1>';
            echo '<p>' . htmlspecialchars($method . ' ' . $uri) . '</p>';
            echo '<p><a href="/public/">Ana sayfaya dön</a></p>';
            return;
        }

        if (is_array($handler)) {
            [$class, $action] = $handler;
            (new $class())->$action();
        } else {
            $handler();
        }
    }
}