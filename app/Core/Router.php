<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private static array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public static function get(string $path, callable|array $action): void
    {
        self::$routes['GET'][] = [$path, $action];
    }

    public static function post(string $path, callable|array $action): void
    {
        self::$routes['POST'][] = [$path, $action];
    }

    public static function resource(string $name, string $controller): void
    {
        self::get('/' . $name, [$controller, 'index']);
        self::get('/' . $name . '/create', [$controller, 'create']);
        self::post('/' . $name, [$controller, 'store']);
        self::get('/' . $name . '/{id}', [$controller, 'show']);
        self::get('/' . $name . '/{id}/edit', [$controller, 'edit']);
        self::post('/' . $name . '/{id}/update', [$controller, 'update']);
        self::post('/' . $name . '/{id}/delete', [$controller, 'destroy']);
    }

    public static function dispatch(string $method, string $uri): void
    {
        $path = rtrim((string) parse_url($uri, PHP_URL_PATH), '/') ?: '/';
        $method = strtoupper($method);
        $routes = self::$routes[$method] ?? [];

        foreach ($routes as [$routePath, $action]) {
            $pattern = self::compilePattern($routePath);
            if (preg_match($pattern, $path, $matches) !== 1) {
                continue;
            }

            $parameters = array_values(array_slice($matches, 1));
            self::execute($action, $parameters);
            return;
        }

        http_response_code(404);
        echo View::render('errors/404', ['title' => 'Page not found', 'path' => $path], null);
    }

    private static function execute(callable|array $action, array $parameters = []): void
    {
        $result = null;

        if (is_array($action)) {
            [$controllerClass, $method] = $action;
            $controller = new $controllerClass();
            $result = $controller->{$method}(...$parameters);
        } else {
            $result = $action(...$parameters);
        }

        if (is_string($result)) {
            echo $result;
        }
    }

    private static function compilePattern(string $path): string
    {
        $quoted = preg_quote($path, '#');
        $quoted = preg_replace('#\\\{[a-zA-Z_][a-zA-Z0-9_]*\\\}#', '([^/]+)', $quoted) ?? $quoted;

        return '#^' . $quoted . '$#';
    }
}
