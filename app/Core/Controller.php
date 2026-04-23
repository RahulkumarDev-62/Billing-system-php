<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $template, array $data = [], ?string $layout = 'layouts/app'): string
    {
        return View::render($template, $data, $layout);
    }
}
