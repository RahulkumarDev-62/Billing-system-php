<?php

declare(strict_types=1);

namespace App\Models;

final class Order extends BaseModel
{
    protected string $table = 'orders';
    protected string $orderBy = 'id DESC';

    public function filter(string $mode, string $status): array
    {
        return $this->whereMany(['payment_mode' => $mode, 'order_status' => $status]);
    }

    public function whereMany(array $criteria): array
    {
        $conditions = [];
        $values = [];

        foreach ($criteria as $column => $value) {
            $conditions[] = $column . ' = :' . $column;
            $values[$column] = $value;
        }

        $statement = \App\Core\Database::connection()->prepare('SELECT * FROM ' . $this->table . ' WHERE ' . implode(' AND ', $conditions) . ' ORDER BY ' . $this->orderBy);
        $statement->execute($values);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
