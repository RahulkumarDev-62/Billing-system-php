<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Model;
use App\Models\Branch;

final class BranchController extends CrudController
{
    protected string $resourceLabel = 'Branches';
    protected string $basePath = '/branches';

    protected function model(): Model
    {
        return new Branch();
    }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Branch Name', 'type' => 'text'],
            ['name' => 'code', 'label' => 'Branch Code', 'type' => 'text'],
            ['name' => 'address', 'label' => 'Address', 'type' => 'text'],
            ['name' => 'phone', 'label' => 'Phone', 'type' => 'text'],
        ];
    }

    protected function rules(): array
    {
        return [
            'name' => 'Branch Name',
            'code' => 'Branch Code',
        ];
    }

    protected function fillable(array $input): array
    {
        return [
            'name' => trim((string) ($input['name'] ?? '')),
            'code' => trim((string) ($input['code'] ?? '')),
            'address' => trim((string) ($input['address'] ?? '')),
            'phone' => trim((string) ($input['phone'] ?? '')),
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
        $name = trim((string) ($input['name'] ?? ''));
        $code = trim((string) ($input['code'] ?? ''));
        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Branch Name is required.';
        }
        if ($code === '') {
            $errors['code'] = 'Branch Code is required.';
        }
        if ((new Branch())->findByCode($code)) {
            $errors['code'] = 'Branch code already exists.';
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
                'submitLabel' => 'Create Branch',
            ]);
        }

        Database::connection()->prepare('INSERT INTO branches (name, code, address, phone) VALUES (:name, :code, :address, :phone)')->execute([
            'name' => $name,
            'code' => $code,
            'address' => trim((string) ($input['address'] ?? '')),
            'phone' => trim((string) ($input['phone'] ?? '')),
        ]);

        flash('success', 'Branch created successfully.');
        redirect($this->basePath);
    }

    public function update(string $id): string
    {
        require_role('admin');
        verify_csrf();

        $input = request();
        $name = trim((string) ($input['name'] ?? ''));
        $code = trim((string) ($input['code'] ?? ''));
        $existing = $this->model()->find($id);
        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Branch Name is required.';
        }
        if ($code === '') {
            $errors['code'] = 'Branch Code is required.';
        }

        $duplicate = (new Branch())->findByCode($code);
        if ($duplicate && (string) $duplicate['id'] !== (string) $id) {
            $errors['code'] = 'Branch code already exists.';
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
                'submitLabel' => 'Update Branch',
            ]);
        }

        Database::connection()->prepare('UPDATE branches SET name = :name, code = :code, address = :address, phone = :phone WHERE id = :id')->execute([
            'name' => $name,
            'code' => $code,
            'address' => trim((string) ($input['address'] ?? ($existing['address'] ?? ''))),
            'phone' => trim((string) ($input['phone'] ?? ($existing['phone'] ?? ''))),
            'id' => $id,
        ]);

        flash('success', 'Branch updated successfully.');
        redirect($this->basePath);
    }
}