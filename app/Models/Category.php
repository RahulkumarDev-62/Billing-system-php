<?php

declare(strict_types=1);

namespace App\Models;

final class Category extends BaseModel
{
    protected string $table = 'categories';
    protected string $orderBy = 'name ASC';

    public function findByName(string $name): ?array
    {
        return $this->firstWhere('name', $name);
    }
}
