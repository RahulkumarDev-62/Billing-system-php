<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $template, array $data = [], ?string $layout = 'layouts/app'): string
    {
        $viewFile = view_path($template . '.php');

        if (!is_file($viewFile)) {
            return '<pre>View not found: ' . e($template) . '</pre>';
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();

        if ($layout === null) {
            return $content;
        }

        $layoutFile = view_path($layout . '.php');
        if (!is_file($layoutFile)) {
            return $content;
        }

        ob_start();
        require $layoutFile;

        return (string) ob_get_clean();
    }
}
