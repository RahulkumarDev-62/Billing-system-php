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
            ['name' => 'image', 'label' => 'Product Image', 'type' => 'file'],
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
        $upload = $this->handleImageUpload();
        if (!empty($upload['error'])) {
            $input['image'] = '';

            return $this->view($this->viewPrefix . '/form', [
                'title' => 'Create ' . $this->resourceLabel,
                'resourceLabel' => $this->resourceLabel,
                'basePath' => $this->basePath,
                'fields' => $this->fieldsWithOptions(),
                'item' => $input,
                'errors' => ['image' => (string) $upload['error']],
                'action' => $this->basePath,
                'submitLabel' => 'Create ' . $this->resourceLabel,
            ]);
        }

        if (!empty($upload['path'])) {
            $input['image'] = (string) $upload['path'];
        }

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
        if ($existing === null) {
            http_response_code(404);
            return 'Product not found';
        }

        $upload = $this->handleImageUpload();
        if (!empty($upload['error'])) {
            $input['image'] = (string) ($existing['image'] ?? '');

            return $this->view($this->viewPrefix . '/form', [
                'title' => 'Edit ' . $this->resourceLabel . ' #' . $id,
                'resourceLabel' => $this->resourceLabel,
                'basePath' => $this->basePath,
                'fields' => $this->fieldsWithOptions(),
                'item' => $input,
                'errors' => ['image' => (string) $upload['error']],
                'action' => $this->basePath . '/' . $id,
                'submitLabel' => 'Update ' . $this->resourceLabel,
            ]);
        }

        if (!empty($upload['path'])) {
            $input['image'] = (string) $upload['path'];
            $this->deleteExistingImage((string) ($existing['image'] ?? ''));
        } else {
            $input['image'] = (string) ($existing['image'] ?? '');
        }

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

    public function createDemoProducts(): void
    {
        require_auth();
        require_admin();
        verify_csrf();

        $connection = Database::connection();
        $categories = new Category();

        $requiredCategories = ['Groceries', 'Beverages', 'Snacks', 'Dairy'];
        $categoryIds = [];
        foreach ($requiredCategories as $categoryName) {
            $category = $categories->findByName($categoryName);
            if ($category === null) {
                $categories->create(['name' => $categoryName]);
                $category = $categories->findByName($categoryName);
            }
            if ($category !== null) {
                $categoryIds[$categoryName] = (int) $category['id'];
            }
        }

        $demoProducts = [
            [
                'name' => 'Basmati Rice 5kg',
                'category' => 'Groceries',
                'price' => 620,
                'cost' => 540,
                'stock' => 45,
                'reorder_level' => 12,
                'image' => '/assets/products/rice.svg',
                'barcode' => '8901001000012',
            ],
            [
                'name' => 'Sunflower Oil 1L',
                'category' => 'Groceries',
                'price' => 165,
                'cost' => 145,
                'stock' => 70,
                'reorder_level' => 18,
                'image' => '/assets/products/oil.svg',
                'barcode' => '8901001000029',
            ],
            [
                'name' => 'Fresh Milk 1L',
                'category' => 'Dairy',
                'price' => 62,
                'cost' => 50,
                'stock' => 90,
                'reorder_level' => 25,
                'image' => '/assets/products/milk.svg',
                'barcode' => '8901001000036',
            ],
            [
                'name' => 'Salted Chips 90g',
                'category' => 'Snacks',
                'price' => 25,
                'cost' => 17,
                'stock' => 130,
                'reorder_level' => 35,
                'image' => '/assets/products/chips.svg',
                'barcode' => '8901001000043',
            ],
            [
                'name' => 'Orange Juice 1L',
                'category' => 'Beverages',
                'price' => 110,
                'cost' => 84,
                'stock' => 60,
                'reorder_level' => 20,
                'image' => '/assets/products/juice.svg',
                'barcode' => '8901001000050',
            ],
        ];

        $created = 0;
        foreach ($demoProducts as $product) {
            $statement = $connection->prepare('SELECT id FROM products WHERE name = :name AND branch_id IS NULL LIMIT 1');
            $statement->execute(['name' => $product['name']]);
            $exists = $statement->fetch(\PDO::FETCH_ASSOC);

            if ($exists) {
                continue;
            }

            $this->model()->create([
                'name' => $product['name'],
                'category_id' => (int) ($categoryIds[$product['category']] ?? 0),
                'branch_id' => null,
                'price' => (float) $product['price'],
                'cost' => (float) $product['cost'],
                'stock' => (int) $product['stock'],
                'reorder_level' => (int) $product['reorder_level'],
                'image' => $product['image'],
                'barcode' => $product['barcode'],
            ]);
            $created++;
        }

        flash('success', $created > 0 ? $created . ' demo products created.' : 'Demo products already exist.');
        redirect('/admin/products');
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

    private function handleImageUpload(): array
    {
        if (!isset($_FILES['image']) || !is_array($_FILES['image'])) {
            return ['path' => null, 'error' => null];
        }

        $file = $_FILES['image'];
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($error === UPLOAD_ERR_NO_FILE) {
            return ['path' => null, 'error' => null];
        }

        if ($error !== UPLOAD_ERR_OK) {
            return ['path' => null, 'error' => 'Image upload failed. Please try again.'];
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');
        if (!is_uploaded_file($tmpPath)) {
            return ['path' => null, 'error' => 'Invalid uploaded file.'];
        }

        $mime = (string) (mime_content_type($tmpPath) ?: '');
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];

        if (!isset($allowed[$mime])) {
            return ['path' => null, 'error' => 'Only JPG, PNG, WEBP, or GIF images are allowed.'];
        }

        $uploadDir = base_path('public/uploads/products');
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            return ['path' => null, 'error' => 'Unable to create upload directory.'];
        }

        $filename = 'product_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
        $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($tmpPath, $targetPath)) {
            return ['path' => null, 'error' => 'Could not save uploaded image.'];
        }

        return ['path' => '/uploads/products/' . $filename, 'error' => null];
    }

    private function deleteExistingImage(string $imagePath): void
    {
        $imagePath = trim($imagePath);
        if ($imagePath === '' || str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return;
        }

        $normalized = ltrim(str_replace('/', DIRECTORY_SEPARATOR, $imagePath), DIRECTORY_SEPARATOR);
        $fullPath = base_path('public' . DIRECTORY_SEPARATOR . $normalized);
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
