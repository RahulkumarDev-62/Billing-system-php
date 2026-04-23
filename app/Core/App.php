<?php

declare(strict_types=1);

namespace App\Core;

final class App
{
    public static function run(string $method, string $uri): void
    {
        Router::dispatch($method, $uri);
    }
}
