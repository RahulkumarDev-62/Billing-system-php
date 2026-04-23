<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Models\Branch;
use App\Models\Product;

final class PanelController extends BaseController
{
    public function adminDashboard(): string
    {
        require_role('admin');

        $stats = $this->stats();

        return $this->view('panels/admin', [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'branches' => Database::connection()->query('SELECT * FROM branches ORDER BY id DESC')->fetchAll(\PDO::FETCH_ASSOC),
            'users' => Database::connection()->query("SELECT * FROM users WHERE role IN ('staff', 'branch') ORDER BY id DESC")->fetchAll(\PDO::FETCH_ASSOC),
        ]);
    }

    public function adminProducts(): string
    {
        require_role('admin');

        return $this->view('panels/admin-products', [
            'title' => 'Admin Products',
            'products' => (new Product())->withCategory(),
        ]);
    }

    public function adminBarcodeGenerator(): string
    {
        require_role('admin');

        return $this->view('panels/admin-barcodes', [
            'title' => 'Barcode Generator',
            'products' => (new Product())->all(),
        ]);
    }

    public function adminStockManagement(): string
    {
        require_role('admin');

        $lowStock = Database::connection()->query('SELECT * FROM products WHERE stock <= reorder_level ORDER BY stock ASC, id DESC')->fetchAll(\PDO::FETCH_ASSOC);

        return $this->view('panels/admin-stock', [
            'title' => 'Stock Management',
            'lowStock' => $lowStock,
        ]);
    }

    public function adminShopsManagement(): string
    {
        require_role('admin');

        return $this->view('panels/admin-shops', [
            'title' => 'Shops Management',
            'branches' => (new Branch())->all(),
        ]);
    }

    public function adminUsersManagement(): string
    {
        require_role('admin');

        $users = Database::connection()->query("SELECT users.*, branches.name AS branch_name FROM users LEFT JOIN branches ON users.branch_id = branches.id WHERE users.role IN ('staff', 'branch') ORDER BY users.id DESC")->fetchAll(\PDO::FETCH_ASSOC);

        return $this->view('panels/admin-users', [
            'title' => 'Users Management',
            'users' => $users,
        ]);
    }

    public function adminSalesReports(): string
    {
        require_role('admin');

        $summary = Database::connection()->query('SELECT DATE(created_at) AS sale_date, COUNT(*) AS bills, SUM(total) AS revenue FROM orders GROUP BY DATE(created_at) ORDER BY sale_date DESC LIMIT 30')->fetchAll(\PDO::FETCH_ASSOC);

        return $this->view('panels/admin-reports', [
            'title' => 'Sales Reports',
            'summary' => $summary,
        ]);
    }

    public function staffBilling(): string
    {
        require_role('staff');

        redirect('/orders/create');
    }

    public function staffTodaySales(): string
    {
        require_role('staff');

        $user = auth_user();
        [$start, $end] = today_date_range();

        $connection = Database::connection();
        $statement = $connection->prepare('SELECT * FROM orders WHERE staff_id = :staff_id AND created_at BETWEEN :start AND :end ORDER BY id DESC');
        $statement->execute([
            'staff_id' => (int) $user['id'],
            'start' => $start,
            'end' => $end,
        ]);

        return $this->view('panels/staff-sales', [
            'title' => 'Today Sales',
            'sales' => $statement->fetchAll(\PDO::FETCH_ASSOC),
        ]);
    }

    public function staffBillHistory(): string
    {
        require_role('staff');

        $user = auth_user();
        $statement = Database::connection()->prepare('SELECT * FROM orders WHERE staff_id = :staff_id ORDER BY id DESC LIMIT 100');
        $statement->execute(['staff_id' => (int) $user['id']]);

        return $this->view('panels/staff-history', [
            'title' => 'Bill History',
            'sales' => $statement->fetchAll(\PDO::FETCH_ASSOC),
        ]);
    }

    public function branchBilling(): string
    {
        require_role('branch');

        redirect('/orders/create');
    }

    public function branchSalesSummary(): string
    {
        require_role('branch');

        $user = auth_user();
        $branchId = (int) ($user['branch_id'] ?? 0);
        $connection = Database::connection();
        $sales = $connection->prepare('SELECT * FROM orders WHERE branch_id = :branch_id ORDER BY id DESC LIMIT 10');
        $sales->execute(['branch_id' => $branchId]);

        return $this->view('panels/branch-summary', [
            'title' => 'Sales Summary',
            'sales' => $sales->fetchAll(\PDO::FETCH_ASSOC),
        ]);
    }

    private function stats(): array
    {
        $connection = Database::connection();

        return [
            'users' => (int) $connection->query('SELECT COUNT(*) AS total FROM users')->fetch(\PDO::FETCH_ASSOC)['total'],
            'branches' => (int) $connection->query('SELECT COUNT(*) AS total FROM branches')->fetch(\PDO::FETCH_ASSOC)['total'],
            'categories' => (int) $connection->query('SELECT COUNT(*) AS total FROM categories')->fetch(\PDO::FETCH_ASSOC)['total'],
            'products' => (int) $connection->query('SELECT COUNT(*) AS total FROM products')->fetch(\PDO::FETCH_ASSOC)['total'],
            'sales' => (int) $connection->query('SELECT COUNT(*) AS total FROM orders')->fetch(\PDO::FETCH_ASSOC)['total'],
        ];
    }
}