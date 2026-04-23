<?php

declare(strict_types=1);

namespace App\Models;

final class User extends BaseModel
{
    protected string $table = 'users';
    protected string $orderBy = 'id DESC';

    public function findByEmail(string $email): ?array
    {
        return $this->firstWhere('email', $email);
    }

    public function findByRole(string $role): array
    {
        return $this->where('role', $role);
    }

    public function findByRoleAndBranch(string $role, ?int $branchId): array
    {
        if ($branchId === null) {
            return $this->where('role', $role);
        }

        $statement = \App\Core\Database::connection()->prepare('SELECT * FROM users WHERE role = :role AND branch_id = :branch_id ORDER BY id DESC');
        $statement->execute(['role' => $role, 'branch_id' => $branchId]);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
