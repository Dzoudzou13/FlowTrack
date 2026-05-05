<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /**
     * @var array<string, array<string, callable|array>>
     */
    private array $routes = [
        'GET'    => [],
        'POST'   => [],
        'PUT'    => [],
        'PATCH'  => [],
        'DELETE' => [],
    ];

    public function get(string $uri, callable|array $action): void
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, callable|array $action): void
    {
        $this->addRoute('POST', $uri, $action);
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = $this->normalizePath($uri);
        $method = strtoupper($method);
        [$action, $params] = $this->resolveRoute($method, $path);

        if ($action === null) {
            http_response_code(404);
            echo '404 | Stranka nebola najdena.';

            return;
        }

        $this->invoke($action, $params);
    }

    private function addRoute(string $method, string $uri, callable|array $action): void
    {
        $this->routes[$method][$this->normalizePath($uri)] = $action;
    }

    private function normalizePath(string $uri): string
    {
        // Upravi URL na jednotny tvar.
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

        if ($base !== '' && $base !== '/' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        $path = '/' . trim($path, '/');

        return $path === '//' ? '/' : $path;
    }

    /**
     * @return array{0: callable|array|null, 1: array<int, string>}
     */
    private function resolveRoute(string $method, string $path): array
    {
        // Najde zhodnu routu.
        $routes = $this->routes[$method] ?? [];

        if (isset($routes[$path])) {
            return [$routes[$path], []];
        }

        foreach ($routes as $route => $action) {
            $pattern = preg_replace(
                '#\\\\\{[a-zA-Z_][a-zA-Z0-9_]*\\\\\}#',
                '([^/]+)',
                preg_quote($route, '#')
            );

            if ($pattern === null) {
                continue;
            }

            if (preg_match('#^' . $pattern . '$#', $path, $matches) === 1) {
                array_shift($matches);

                return [$action, $matches];
            }
        }

        return [null, []];
    }

    private function invoke(callable|array $action, array $params = []): void
    {
        // Spusti controller alebo callback.
        if (is_array($action) && count($action) === 2) {
            [$controller, $method] = $action;
            $controller->{$method}(...$params);

            return;
        }

        call_user_func_array($action, $params);
    }
}
