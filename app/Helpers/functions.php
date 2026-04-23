<?php

declare(strict_types=1);

use App\Core\Database;

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return defined('BASE_PATH')
            ? BASE_PATH . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : '')
            : $path;
    }
}

if (!function_exists('request')) {
    function request(): array
    {
        return array_merge($_GET, $_POST);
    }
}

if (!function_exists('method')) {
    function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(16));
        }

        return (string) $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(): void
    {
        if (method() !== 'POST') {
            return;
        }

        $token = $_POST['_token'] ?? '';
        if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
            http_response_code(419);
            exit('Invalid CSRF token');
        }
    }
}

if (!function_exists('flash')) {
    function flash(string $key, mixed $value = null): mixed
    {
        if (func_num_args() === 2) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }

        if (!isset($_SESSION['_flash'][$key])) {
            return null;
        }

        $value = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);

        return $value;
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        return $_SESSION['_old_input'][$key] ?? $default;
    }
}

if (!function_exists('set_old_input')) {
    function set_old_input(array $input): void
    {
        $_SESSION['_old_input'] = $input;
    }
}

if (!function_exists('clear_old_input')) {
    function clear_old_input(): void
    {
        unset($_SESSION['_old_input']);
    }
}

if (!function_exists('response_json')) {
    function response_json(array $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

if (!function_exists('app_path')) {
    function app_path(string $path = ''): string
    {
        return base_path('app' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}

if (!function_exists('auth_user')) {
    function auth_user(): ?array
    {
        if (empty($_SESSION['auth_user_id'])) {
            return null;
        }

        try {
            $statement = Database::connection()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
            $statement->execute(['id' => (int) $_SESSION['auth_user_id']]);
            $user = $statement->fetch(\PDO::FETCH_ASSOC);

            return $user ?: null;
        } catch (\Throwable) {
            return null;
        }
    }
}

if (!function_exists('auth_check')) {
    function auth_check(): bool
    {
        return auth_user() !== null;
    }
}

if (!function_exists('auth_is_admin')) {
    function auth_is_admin(): bool
    {
        $user = auth_user();

        return (string) ($user['role'] ?? '') === 'admin';
    }
}

if (!function_exists('auth_role')) {
    function auth_role(): string
    {
        $user = auth_user();

        return (string) ($user['role'] ?? '');
    }
}

if (!function_exists('require_auth')) {
    function require_auth(): void
    {
        if (!auth_check()) {
            redirect('/');
        }
    }
}

if (!function_exists('require_admin')) {
    function require_admin(): void
    {
        require_auth();

        if (!auth_is_admin()) {
            http_response_code(403);
            exit('Forbidden');
        }
    }
}

if (!function_exists('require_role')) {
    function require_role(string|array $roles): void
    {
        require_auth();

        $roles = is_array($roles) ? $roles : [$roles];
        if (!in_array(auth_role(), $roles, true)) {
            http_response_code(403);
            exit('Forbidden');
        }
    }
}

if (!function_exists('config_path')) {
    function config_path(string $path = ''): string
    {
        return base_path('config' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}

if (!function_exists('view_path')) {
    function view_path(string $path = ''): string
    {
        return base_path('resources' . DIRECTORY_SEPARATOR . 'views' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        static $items = null;

        if ($items === null) {
            $items = [];
            foreach (glob(config_path('*.php')) ?: [] as $file) {
                $items[pathinfo($file, PATHINFO_FILENAME)] = require $file;
            }
        }

        $value = $items;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}

if (!function_exists('asset')) {
    function asset(string $path = ''): string
    {
        $baseUrl = rtrim((string) config('app.url', ''), '/');
        $trimmed = ltrim($path, '/');

        return $trimmed === '' ? $baseUrl : $baseUrl . '/' . $trimmed;
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $baseUrl = rtrim((string) config('app.url', ''), '/');
        $trimmed = ltrim($path, '/');

        return $trimmed === '' ? $baseUrl : $baseUrl . '/' . $trimmed;
    }
}

if (!function_exists('e')) {
    function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }
}

if (!function_exists('role_dashboard_path')) {
    function role_dashboard_path(?string $role = null): string
    {
        $role = $role ?? auth_role();

        return match ($role) {
            'admin' => '/admin/dashboard',
            'staff' => '/staff/billing',
            'branch' => '/branch/billing',
            default => '/',
        };
    }
}

if (!function_exists('today_date_range')) {
    function today_date_range(): array
    {
        $start = (new DateTimeImmutable('today'))->format('Y-m-d 00:00:00');
        $end = (new DateTimeImmutable('today'))->format('Y-m-d 23:59:59');

        return [$start, $end];
    }
}
