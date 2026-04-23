<?php

declare(strict_types=1);

namespace App\Models;

final class Product extends BaseModel
{
    protected string $table = 'products';
    protected string $orderBy = 'id DESC';

    public function withCategory(): array
    {
        $statement = \App\Core\Database::connection()->query('SELECT products.*, categories.name AS category_name, branches.name AS branch_name FROM products LEFT JOIN categories ON products.category_id = categories.id LEFT JOIN branches ON products.branch_id = branches.id ORDER BY products.id DESC');

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findByBarcode(string $barcode): ?array
    {
        return $this->firstWhere('barcode', $barcode);
    }

    public function generateBarcode(): string
    {
        do {
            $barcode = (string) random_int(100000000000, 999999999999);
        } while ($this->findByBarcode($barcode) !== null);

        return $barcode;
    }
}
