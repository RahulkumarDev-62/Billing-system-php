<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Model;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;

final class ProductController extends CrudController
{
    protected string $resourceLabel = 'Products';
    protected string $basePath = '/products';

    protected function model(): Model
    {
        return new Product();
    }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Product Name', 'type' => 'text'],
            ['name' => 'category_id', 'label' => 'Category', 'type' => 'select'],
            ['name' => 'branch_id', 'label' => 'Branch', 'type' => 'select'],
            ['name' => 'price', 'label' => 'Price', 'type' => 'number', 'step' => '0.01'],
            ['name' => 'cost', 'label' => 'Cost Price', 'type' => 'number', 'step' => '0.01'],
            ['name' => 'stock', 'label' => 'Stock', 'type' => 'number'],
            ['name' => 'reorder_level', 'label' => 'Reorder Level', 'type' => 'number'],
            ['name' => 'image', 'label' => 'Image URL', 'type' => 'text'],
            ['name' => 'barcode', 'label' => 'Barcode', 'type' => 'text'],
        ];
    }

    protected function rules(): array
    {
        return [
            'name' => 'Product Name',
            'category_id' => 'Category',
            'price' => 'Price',
            'stock' => 'Stock',
        ];
    }

    protected function fillable(array $input): array
    {
        return [
            'name' => trim((string) ($input['name'] ?? '')),
            'category_id' => (int) ($input['category_id'] ?? 0),
            'branch_id' => $this->normalizeBranchId($input['branch_id'] ?? null),
            'price' => (float) ($input['price'] ?? 0),
            'cost' => (float) ($input['cost'] ?? 0),
            'stock' => (int) ($input['stock'] ?? 0),
            'reorder_level' => (int) ($input['reorder_level'] ?? 0),
            'image' => trim((string) ($input['image'] ?? '')),
            'barcode' => trim((string) ($input['barcode'] ?? '')),
        ];
    }

    public function index(): string
    {
        require_role(['admin', 'staff', 'branch']);

        return parent::index();
    }

    public function create(): string
    {
        require_auth();
        require_admin();

        return $this->view($this->viewPrefix . '/form', [
            'title' => 'Create ' . $this->resourceLabel,
            'resourceLabel' => $this->resourceLabel,
            'basePath' => $this->basePath,
            'fields' => $this->fieldsWithOptions(),
            'item' => [],
            'errors' => [],
            'action' => $this->basePath,
            'submitLabel' => 'Create ' . $this->resourceLabel,
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
            'fields' => $this->fieldsWithOptions(),
            'item' => $this->model()->find($id) ?? [],
            'errors' => [],
            'action' => $this->basePath . '/' . $id,
            'submitLabel' => 'Update ' . $this->resourceLabel,
        ]);
    }

    public function store(): string
    {
        require_auth();
        require_admin();
        verify_csrf();

        $input = request();
        if (trim((string) ($input['barcode'] ?? '')) === '') {
            $input['barcode'] = (new Product())->generateBarcode();
        }
        $errors = $this->validate($input);

        $duplicate = Database::connection()->prepare('SELECT id FROM products WHERE name = :name AND ((branch_id IS NULL AND :branch_id IS NULL) OR branch_id = :branch_id) LIMIT 1');
        $duplicate->execute([
            'name' => trim((string) ($input['name'] ?? '')),
            'branch_id' => $this->normalizeBranchId($input['branch_id'] ?? null),
        ]);
        if ($duplicate->fetch(\PDO::FETCH_ASSOC)) {
            $errors['name'] = 'Duplicate product name for this shop is not allowed.';
        }

        if ($errors) {
            return $this->view($this->viewPrefix . '/form', [
                'title' => 'Create ' . $this->resourceLabel,
                'resourceLabel' => $this->resourceLabel,
                'basePath' => $this->basePath,
                'fields' => $this->fieldsWithOptions(),
                'item' => $input,
                'errors' => $errors,
                'action' => $this->basePath,
                'submitLabel' => 'Create ' . $this->resourceLabel,
            ]);
        }

        $this->model()->create($this->fillable($input));
        flash('success', 'Product created successfully.');
        redirect($this->basePath);
    }

    public function update(string $id): string
    {
        require_auth();
        require_admin();
        verify_csrf();

        $input = request();
        $existing = $this->model()->find($id);
        if ($existing && trim((string) ($input['barcode'] ?? '')) === '') {
            $input['barcode'] = (string) $existing['barcode'];
        }
        $errors = $this->validate($input);

        $duplicate = Database::connection()->prepare('SELECT id FROM products WHERE name = :name AND ((branch_id IS NULL AND :branch_id IS NULL) OR branch_id = :branch_id) AND id <> :id LIMIT 1');
        $duplicate->execute([
            'name' => trim((string) ($input['name'] ?? '')),
            'branch_id' => $this->normalizeBranchId($input['branch_id'] ?? null),
            'id' => $id,
        ]);
        if ($duplicate->fetch(\PDO::FETCH_ASSOC)) {
            $errors['name'] = 'Duplicate product name for this shop is not allowed.';
        }

        if ($errors) {
            return $this->view($this->viewPrefix . '/form', [
                'title' => 'Edit ' . $this->resourceLabel . ' #' . $id,
                'resourceLabel' => $this->resourceLabel,
                'basePath' => $this->basePath,
                'fields' => $this->fieldsWithOptions(),
                'item' => $input,
                'errors' => $errors,
                'action' => $this->basePath . '/' . $id,
                'submitLabel' => 'Update ' . $this->resourceLabel,
            ]);
        }

        $this->model()->update($id, $this->fillable($input));
        flash('success', 'Product updated successfully.');
        redirect($this->basePath);
    }

    public function jsonIndex(): void
    {
        response_json(['success' => true, 'products' => (new Product())->withCategory()]);
    }

    public function barcode(string $id): string
    {
        require_role(['admin', 'staff', 'branch']);

        $product = $this->model()->find($id);
        if (!$product) {
            http_response_code(404);
            return 'Product not found';
        }

        return $this->view('products/barcode', [
            'title' => 'Barcode ' . $product['name'],
            'product' => $product,
        ]);
    }

    public function scanner(string $id): void
    {
        $product = $this->model()->find($id);

        if (!$product) {
            response_json(['success' => false, 'message' => 'Product not found'], 404);
        }

        response_json(['success' => true, 'data' => $product]);
    }

    private function fieldsWithOptions(): array
    {
        $categories = (new Category())->all();
        $branches = (new Branch())->all();

        $fields = $this->fields();
        foreach ($fields as &$field) {
            if ($field['name'] === 'category_id') {
                $field['options'] = array_map(static fn (array $category): array => ['value' => (string) $category['id'], 'label' => (string) $category['name']], $categories);
            }
            if ($field['name'] === 'branch_id') {
                $field['options'] = array_merge([
                    ['value' => '', 'label' => 'All branches'],
                ], array_map(static fn (array $branch): array => ['value' => (string) $branch['id'], 'label' => (string) $branch['name']], $branches));
            }
        }

        return $fields;
    }

    private function normalizeBranchId(mixed $value): ?int
    {
        $branchId = (int) ($value ?? 0);

        return $branchId > 0 ? $branchId : null;
    }
}
