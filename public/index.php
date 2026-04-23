<?php
declare(strict_types=1);

session_start();
date_default_timezone_set('UTC');
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'app');
define('CONFIG_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'config');
define('RESOURCES_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'resources');
define('VIEWS_PATH', RESOURCES_PATH . DIRECTORY_SEPARATOR . 'views');

require BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'functions.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $filePath = APP_PATH . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

    if (is_file($filePath)) {
        require $filePath;
    }
});

require BASE_PATH . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'web.php';

\App\Core\App::run($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
