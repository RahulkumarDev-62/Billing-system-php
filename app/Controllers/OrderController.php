<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Model;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Product;

final class OrderController extends CrudController
{
    protected string $resourceLabel = 'Sales';
    protected string $basePath = '/orders';

    protected function model(): Model
    {
        return new Order();
    }

    public function index(): string
    {
        require_auth();

        $statement = Database::connection()->query('SELECT orders.*, branches.name AS branch_name, users.name AS staff_name FROM orders LEFT JOIN branches ON orders.branch_id = branches.id LEFT JOIN users ON orders.staff_id = users.id ORDER BY orders.id DESC');
        $sales = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $this->view('orders/index', [
            'title' => 'Sales',
            'orders' => $sales,
            'basePath' => $this->basePath,
        ]);
    }

    public function create(): string
    {
        require_role(['admin', 'staff', 'branch']);

        $user = auth_user();
        $branchId = (int) ($user['branch_id'] ?? 0);
        $isAdmin = auth_role() === 'admin';

        return $this->view('orders/form', [
            'title' => 'Create Sale',
            'action' => $this->basePath,
            'submitLabel' => 'Save Sale',
            'products' => (new Product())->all(),
            'branches' => (new Branch())->all(),
            'selectedBranchId' => $branchId,
            'canSelectBranch' => $isAdmin,
            'errors' => [],
            'item' => [],
        ]);
    }

    public function store(): string
    {
        require_role(['admin', 'staff', 'branch']);
        verify_csrf();

        $input = request();
        $items = $input['items'] ?? [];
        $receiptNo = trim((string) ($input['receipt_no'] ?? ''));
        $customerName = trim((string) ($input['customer_name'] ?? ''));
        $customerMobile = trim((string) ($input['customer_mobile'] ?? ''));
        $customerEmail = trim((string) ($input['customer_email'] ?? ''));
        $paymentMode = strtolower(trim((string) ($input['payment_mode'] ?? 'cash')));
        $currentUser = auth_user();
        $branchId = auth_role() === 'admin'
            ? (int) ($input['branch_id'] ?? 0)
            : (int) ($currentUser['branch_id'] ?? 0);

        $errors = [];
        if (!is_array($items) || $items === []) {
            $errors['items'] = 'Add at least one product line.';
        }
        if ($branchId <= 0) {
            $errors['branch_id'] = 'Branch is required.';
        }

        $productModel = new Product();
        $preparedItems = [];
        $subtotal = 0.0;
        $discountTotal = 0.0;

        foreach ($items as $line) {
            $lineBarcode = trim((string) ($line['barcode'] ?? ''));
            $productId = (int) ($line['product_id'] ?? 0);
            $quantity = max(1, (int) ($line['quantity'] ?? 0));
            $discountPercent = max(0.0, (float) ($line['discount_percent'] ?? 0));
            $product = $lineBarcode !== '' ? $productModel->findByBarcode($lineBarcode) : $productModel->find($productId);

            if (!$product) {
                $errors['items'] = 'A selected product could not be found.';
                continue;
            }

            if ((int) $product['stock'] < $quantity) {
                $errors['items'] = 'Insufficient stock for ' . $product['name'] . '.';
                continue;
            }

            $lineTotal = (float) $product['price'] * $quantity;
            $lineDiscount = ($lineTotal * $discountPercent) / 100;
            $subtotal += $lineTotal;
            $discountTotal += $lineDiscount;

            $preparedItems[] = [
                'product_id' => (int) $product['id'],
                'barcode' => (string) $product['barcode'],
                'product_name' => $product['name'],
                'quantity' => $quantity,
                'price' => (float) $product['price'],
                'discount_percent' => $discountPercent,
                'line_total' => round($lineTotal - $lineDiscount, 2),
            ];
        }

        if ($errors) {
            return $this->view('orders/form', [
                'title' => 'Create Sale',
                'action' => $this->basePath,
                'submitLabel' => 'Save Sale',
                'products' => (new Product())->all(),
                'branches' => (new Branch())->all(),
                'selectedBranchId' => $branchId,
                'canSelectBranch' => auth_role() === 'admin',
                'errors' => $errors,
                'item' => $input,
            ]);
        }

        $orderDetails = [
            'items' => $preparedItems,
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discountTotal, 2),
            'total' => round($subtotal - $discountTotal, 2),
        ];

        $connection = Database::connection();
        $orderNo = (int) ($connection->query('SELECT COALESCE(MAX(order_no), 0) + 1 AS next_no FROM orders')->fetch(\PDO::FETCH_ASSOC)['next_no'] ?? 1);
        $userId = (int) ($currentUser['id'] ?? 0);
        $status = $paymentMode === 'online' ? 'pending' : 'success';

        $this->model()->create([
            'order_no' => $orderNo,
            'staff_id' => $userId,
            'branch_id' => $branchId,
            'customer_name' => $customerName,
            'customer_mobile' => $customerMobile,
            'customer_email' => $customerEmail,
            'payment_mode' => $paymentMode,
            'order_status' => $status,
            'subtotal' => $orderDetails['subtotal'],
            'discount_total' => $orderDetails['discount_total'],
            'total' => $orderDetails['total'],
            'order_details' => json_encode($orderDetails, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'receipt_no' => $receiptNo !== '' ? $receiptNo : 'SALE-' . $orderNo,
        ]);

        $orderId = (int) $connection->lastInsertId();

        if ($paymentMode === 'cash') {
            $this->deductStock($preparedItems);
        }

        flash('success', 'Sale created successfully.');
        redirect($this->basePath . '/' . $orderId);
    }

    public function show(string $id): string
    {
        require_auth();

        $order = $this->model()->find($id);
        if (!$order) {
            http_response_code(404);
            return 'Order not found';
        }

        return $this->view('orders/show', [
            'title' => 'Sale #' . $order['order_no'],
            'order' => $order,
            'details' => json_decode((string) $order['order_details'], true) ?: [],
        ]);
    }

    public function pay(string $id): string
    {
        require_auth();
        verify_csrf();

        $order = $this->model()->find($id);
        if (!$order) {
            http_response_code(404);
            return 'Order not found';
        }

        $details = json_decode((string) $order['order_details'], true) ?: [];
        $items = $details['items'] ?? [];
        $this->deductStock($items);

        $statement = Database::connection()->prepare('UPDATE orders SET order_status = :status WHERE id = :id');
        $statement->execute(['status' => 'success', 'id' => $id]);

        flash('success', 'Payment confirmed.');
        redirect($this->basePath . '/' . $id);
    }

    public function cancel(string $id): string
    {
        require_auth();
        require_admin();
        verify_csrf();

        $statement = Database::connection()->prepare('UPDATE orders SET order_status = :status WHERE id = :id');
        $statement->execute(['status' => 'cancelled', 'id' => $id]);

        flash('success', 'Order cancelled.');
        redirect($this->basePath . '/' . $id);
    }

    public function filter(string $mode, string $status): void
    {
        require_auth();

        $statement = Database::connection()->prepare('SELECT * FROM orders WHERE payment_mode = :mode AND order_status = :status ORDER BY id DESC');
        $statement->execute(['mode' => $mode, 'status' => $status]);
        response_json(['success' => true, 'message' => 'Data fetched successfully', 'orders' => $statement->fetchAll(\PDO::FETCH_ASSOC)]);
    }

    public function countSummary(): void
    {
        require_auth();

        $statement = Database::connection()->query('SELECT payment_mode, order_status, COUNT(*) AS total FROM orders GROUP BY payment_mode, order_status');
        response_json(['success' => true, 'count' => $statement->fetchAll(\PDO::FETCH_ASSOC)]);
    }

    public function operatorOrders(): void
    {
        require_auth();

        $ids = request()['ids'] ?? [];
        if (!is_array($ids) || $ids === []) {
            response_json(['success' => true, 'orders' => []]);
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $statement = Database::connection()->prepare('SELECT * FROM orders WHERE id IN (' . $placeholders . ') ORDER BY id DESC');
        $statement->execute(array_values($ids));

        response_json(['success' => true, 'orders' => $statement->fetchAll(\PDO::FETCH_ASSOC)]);
    }

    private function deductStock(array $items): void
    {
        $productModel = new Product();
        foreach ($items as $item) {
            $product = $productModel->find((int) ($item['product_id'] ?? 0));
            if (!$product) {
                continue;
            }

            $remaining = max(0, (int) $product['stock'] - (int) ($item['quantity'] ?? 0));
            $productModel->update((int) $product['id'], [
                'name' => $product['name'],
                'category_id' => (int) $product['category_id'],
                'branch_id' => (int) ($product['branch_id'] ?? 0),
                'price' => (float) $product['price'],
                'cost' => (float) ($product['cost'] ?? 0),
                'stock' => $remaining,
                'reorder_level' => (int) ($product['reorder_level'] ?? 0),
                'image' => (string) ($product['image'] ?? ''),
                'barcode' => (string) ($product['barcode'] ?? ''),
            ]);
        }
    }
}
