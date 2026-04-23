<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Model;

abstract class CrudController extends BaseController
{
    protected string $resourceLabel = 'Items';
    protected string $basePath = '/';
    protected string $viewPrefix = 'crud';

    abstract protected function model(): Model;

    protected function fields(): array
    {
        return [];
    }

    protected function fillable(array $input): array
    {
        return $input;
    }

    protected function rules(): array
    {
        return [];
    }

    protected function validate(array $input): array
    {
        $errors = [];

        foreach ($this->rules() as $field => $label) {
            $value = $input[$field] ?? null;
            if ($value === null || $value === '') {
                $errors[$field] = $label . ' is required.';
            }
        }

        return $errors;
    }

    public function index(): string
    {
        require_auth();

        return $this->view($this->viewPrefix . '/index', [
            'title' => $this->resourceLabel,
            'resourceLabel' => $this->resourceLabel,
            'basePath' => $this->basePath,
            'fields' => $this->fields(),
            'items' => $this->model()->all(),
        ]);
    }

    public function create(): string
    {
        require_auth();

        return $this->view($this->viewPrefix . '/form', [
            'title' => 'Create ' . $this->resourceLabel,
            'resourceLabel' => $this->resourceLabel,
            'basePath' => $this->basePath,
            'fields' => $this->fields(),
            'item' => [],
            'errors' => [],
            'action' => $this->basePath,
            'submitLabel' => 'Create ' . $this->resourceLabel,
        ]);
    }

    public function store(): string
    {
        require_auth();
        require_admin();
        verify_csrf();

        $input = request();
        $errors = $this->validate($input);
        if ($errors) {
            set_old_input($input);

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

        $this->model()->create($this->fillable($input));
        clear_old_input();
        flash('success', $this->resourceLabel . ' created successfully.');
        redirect($this->basePath);
    }

    public function show(string $id): string
    {
        require_auth();

        return $this->view($this->viewPrefix . '/index', [
            'title' => $this->resourceLabel . ' #' . $id,
            'resourceLabel' => $this->resourceLabel,
            'basePath' => $this->basePath,
            'fields' => $this->fields(),
            'items' => [$this->model()->find($id)],
        ]);
    }

    public function edit(string $id): string
    {
        require_auth();
        require_admin();

        return $this->view($this->viewPrefix . '/form', [
            'title' => 'Edit ' . $this->resourceLabel . ' #' . $id,
            'resourceLabel' => $this->resourceLabel,
            'basePath' => $this->basePath,
            'fields' => $this->fields(),
            'item' => $this->model()->find($id) ?? [],
            'errors' => [],
            'action' => $this->basePath . '/' . $id,
            'submitLabel' => 'Update ' . $this->resourceLabel,
        ]);
    }

    public function update(string $id): string
    {
        require_auth();
        require_admin();
        verify_csrf();

        $input = request();
        $errors = $this->validate($input);
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

        $this->model()->update($id, $this->fillable($input));
        flash('success', $this->resourceLabel . ' updated successfully.');
        redirect($this->basePath);
    }

    public function destroy(string $id): string
    {
        require_auth();
        require_admin();
        verify_csrf();

        $this->model()->delete($id);
        flash('success', $this->resourceLabel . ' deleted successfully.');
        redirect($this->basePath);
    }
}
