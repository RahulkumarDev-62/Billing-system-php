<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Model;
use App\Models\Branch;
use App\Models\User;

final class UserController extends CrudController
{
    protected string $resourceLabel = 'Accounts';
    protected string $basePath = '/users';

    protected function model(): Model
    {
        return new User();
    }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Full Name', 'type' => 'text'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ['name' => 'password', 'label' => 'Password', 'type' => 'password'],
            ['name' => 'role', 'label' => 'Role', 'type' => 'select', 'options' => [
                ['value' => 'staff', 'label' => 'Shop Staff'],
                ['value' => 'branch', 'label' => 'Branch Staff'],
            ]],
            ['name' => 'branch_id', 'label' => 'Branch', 'type' => 'select', 'options' => $this->branchOptions()],
        ];
    }

    protected function rules(): array
    {
        return [
            'name' => 'Full Name',
            'email' => 'Email',
            'password' => 'Password',
            'role' => 'Role',
        ];
    }

    protected function fillable(array $input): array
    {
        $password = trim((string) ($input['password'] ?? ''));

        return [
            'name' => trim((string) ($input['name'] ?? '')),
            'email' => trim((string) ($input['email'] ?? '')),
            'password' => $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : '',
            'role' => in_array(($input['role'] ?? ''), ['staff', 'branch'], true) ? (string) $input['role'] : 'staff',
            'branch_id' => $this->normalizeBranchId($input['branch_id'] ?? null),
            'image' => '',
            'is_active' => 1,
        ];
    }

    public function index(): string
    {
        require_role('admin');

        return parent::index();
    }

    public function store(): string
    {
        require_role('admin');
        verify_csrf();

        $input = request();
        $errors = $this->validate($input);
        $role = (string) ($input['role'] ?? '');
        if (!in_array($role, ['staff', 'branch'], true)) {
            $errors['role'] = 'Only shop staff or branch accounts can be created here.';
        }
        if (trim((string) ($input['password'] ?? '')) === '') {
            $errors['password'] = 'Password is required.';
        }
        if ((new User())->findByEmail(trim((string) ($input['email'] ?? '')))) {
            $errors['email'] = 'Email already exists.';
        }

        if ($errors) {
            return $this->view($this->viewPrefix . '/form', [
                'title' => 'Create ' . $this->resourceLabel,
                'resourceLabel' => $this->resourceLabel,
                'basePath' => $this->basePath,
                'fields' => $this->fields(),
                'item' => $input,
                'errors' => $errors,
                'action' => $this->basePath,
                'submitLabel' => 'Create Account',
            ]);
        }

        $this->model()->create($this->fillable($input));
        flash('success', 'Account created successfully.');
        redirect($this->basePath);
    }

    public function update(string $id): string
    {
        require_role('admin');
        verify_csrf();

        $input = request();
        $existing = $this->model()->find($id);
        if (!$existing) {
            http_response_code(404);
            return 'Account not found';
        }

        $errors = [];
        foreach (['name' => 'Full Name', 'email' => 'Email', 'role' => 'Role'] as $field => $label) {
            if (trim((string) ($input[$field] ?? '')) === '') {
                $errors[$field] = $label . ' is required.';
            }
        }

        $role = (string) ($input['role'] ?? '');
        if (!in_array($role, ['staff', 'branch'], true)) {
            $errors['role'] = 'Only shop staff or branch accounts can be updated here.';
        }

        if ($errors) {
            return $this->view($this->viewPrefix . '/form', [
                'title' => 'Edit ' . $this->resourceLabel . ' #' . $id,
                'resourceLabel' => $this->resourceLabel,
                'basePath' => $this->basePath,
                'fields' => $this->fields(),
                'item' => $input,
                'errors' => $errors,
                'action' => $this->basePath . '/' . $id,
                'submitLabel' => 'Update Account',
            ]);
        }

        $password = trim((string) ($input['password'] ?? ''));
        $payload = [
            'name' => trim((string) $input['name']),
            'email' => trim((string) $input['email']),
            'password' => $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : (string) $existing['password'],
            'role' => $role,
            'branch_id' => $this->normalizeBranchId($input['branch_id'] ?? null),
            'image' => (string) ($existing['image'] ?? ''),
            'is_active' => 1,
        ];

        Database::connection()->prepare('UPDATE users SET name = :name, email = :email, password = :password, role = :role, branch_id = :branch_id, image = :image, is_active = :is_active WHERE id = :id')->execute([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => $payload['password'],
            'role' => $payload['role'],
            'branch_id' => $payload['branch_id'],
            'image' => $payload['image'],
            'is_active' => $payload['is_active'],
            'id' => $id,
        ]);

        flash('success', 'Account updated successfully.');
        redirect($this->basePath);
    }

    public function operators(string $type): void
    {
        require_auth();

        $type = strtolower(trim($type));
        $role = $type === 'branch' ? 'branch' : 'staff';
        $statement = Database::connection()->prepare('SELECT * FROM users WHERE role = :role ORDER BY id DESC');
        $statement->execute(['role' => $role]);

        response_json(['success' => true, 'message' => 'Operators found', 'operators' => $statement->fetchAll(\PDO::FETCH_ASSOC)]);
    }

    public function operatorProfile(string $id): void
    {
        require_auth();

        $user = (new User())->find($id);
        response_json(['success' => true, 'message' => 'Operator found', 'operator' => $user]);
    }

    private function branchOptions(): array
    {
        $branches = (new Branch())->all();

        $options = [['value' => '', 'label' => 'No branch']];
        foreach ($branches as $branch) {
            $options[] = [
                'value' => (string) $branch['id'],
                'label' => (string) $branch['name'],
            ];
        }

        return $options;
    }

    private function normalizeBranchId(mixed $value): ?int
    {
        $branchId = (int) ($value ?? 0);

        return $branchId > 0 ? $branchId : null;
    }
}