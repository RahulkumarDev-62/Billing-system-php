<?php

declare(strict_types=1);

namespace App\Models;

final class Branch extends BaseModel
{
    protected string $table = 'branches';
    protected string $orderBy = 'id DESC';

    public function findByCode(string $code): ?array
    {
        return $this->firstWhere('code', $code);
    }
}