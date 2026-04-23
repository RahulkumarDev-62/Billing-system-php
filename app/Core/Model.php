<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

abstract class Model
{
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected string $orderBy = 'id DESC';

    public function all(): array
    {
        $statement = Database::connection()->query('SELECT * FROM ' . $this->table . ' ORDER BY ' . $this->orderBy);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int|string $id): ?array
    {
        $statement = Database::connection()->prepare('SELECT * FROM ' . $this->table . ' WHERE ' . $this->primaryKey . ' = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function where(string $column, mixed $value): array
    {
        $statement = Database::connection()->prepare('SELECT * FROM ' . $this->table . ' WHERE ' . $column . ' = :value ORDER BY ' . $this->orderBy);
        $statement->execute(['value' => $value]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function firstWhere(string $column, mixed $value): ?array
    {
        $statement = Database::connection()->prepare('SELECT * FROM ' . $this->table . ' WHERE ' . $column . ' = :value LIMIT 1');
        $statement->execute(['value' => $value]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function count(): int
    {
        $statement = Database::connection()->query('SELECT COUNT(*) AS total FROM ' . $this->table);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return (int) ($row['total'] ?? 0);
    }

    public function create(array $data): bool
    {
        $columns = array_keys($data);
        $placeholders = array_map(static fn (string $column): string => ':' . $column, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $statement = Database::connection()->prepare($sql);

        return $statement->execute($data);
    }

    public function createAndGetId(array $data): int
    {
        $this->create($data);

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int|string $id, array $data): bool
    {
        $assignments = array_map(static fn (string $column): string => $column . ' = :' . $column, array_keys($data));
        $data['id'] = $id;

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = :id',
            $this->table,
            implode(', ', $assignments),
            $this->primaryKey
        );

        $statement = Database::connection()->prepare($sql);

        return $statement->execute($data);
    }

    public function delete(int|string $id): bool
    {
        $statement = Database::connection()->prepare('DELETE FROM ' . $this->table . ' WHERE ' . $this->primaryKey . ' = :id');

        return $statement->execute(['id' => $id]);
    }
}
