<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Model;
use App\Models\Category;

final class CategoryController extends CrudController
{
    protected string $resourceLabel = 'Categories';
    protected string $basePath = '/categories';

    protected function model(): Model
    {
        return new Category();
    }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Category Name', 'type' => 'text'],
        ];
    }

    protected function rules(): array
    {
        return ['name' => 'Category Name'];
    }

    protected function fillable(array $input): array
    {
        return ['name' => trim((string) ($input['name'] ?? ''))];
    }

    public function store(): string
    {
        require_auth();
        require_admin();
        verify_csrf();

        $input = request();
        $name = trim((string) ($input['name'] ?? ''));
        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Category Name is required.';
        }

        if ((new Category())->findByName($name)) {
            $errors['name'] = 'Category name already exists.';
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
                'submitLabel' => 'Create ' . $this->resourceLabel,
            ]);
        }

        Database::connection()->prepare('INSERT INTO categories (name) VALUES (:name)')->execute(['name' => $name]);
        flash('success', 'Category created successfully.');
        redirect($this->basePath);
    }

    public function update(string $id): string
    {
        require_auth();
        require_admin();
        verify_csrf();

        $input = request();
        $name = trim((string) ($input['name'] ?? ''));
        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Category Name is required.';
        }

        $existing = (new Category())->findByName($name);
        if ($existing && (string) $existing['id'] !== (string) $id) {
            $errors['name'] = 'Category name already exists.';
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
                'submitLabel' => 'Update ' . $this->resourceLabel,
            ]);
        }

        Database::connection()->prepare('UPDATE categories SET name = :name WHERE id = :id')->execute([
            'name' => $name,
            'id' => $id,
        ]);

        flash('success', 'Category updated successfully.');
        redirect($this->basePath);
    }
}
