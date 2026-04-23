<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\HomeController;
use App\Controllers\PanelController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Controllers\BranchController;
use App\Controllers\UserController;
use App\Core\Router;

Router::get('/', [HomeController::class, 'index']);

Router::get('/admin-login', [AuthController::class, 'adminLoginForm']);
Router::post('/admin-login', [AuthController::class, 'adminLogin']);
Router::get('/staff-login', [AuthController::class, 'staffLoginForm']);
Router::post('/staff-login', [AuthController::class, 'staffLogin']);
Router::post('/staff-login/request-otp', [AuthController::class, 'staffRequestOtp']);
Router::post('/staff-login/verify-otp', [AuthController::class, 'staffVerifyOtp']);
Router::get('/branch-login', [AuthController::class, 'branchLoginForm']);
Router::post('/branch-login', [AuthController::class, 'branchLogin']);
Router::post('/branch-login/request-otp', [AuthController::class, 'branchRequestOtp']);
Router::post('/branch-login/verify-otp', [AuthController::class, 'branchVerifyOtp']);
Router::get('/logout', [AuthController::class, 'logout']);

Router::get('/admin/dashboard', [PanelController::class, 'adminDashboard']);
Router::get('/admin/products', [PanelController::class, 'adminProducts']);
Router::get('/admin/barcode-generator', [PanelController::class, 'adminBarcodeGenerator']);
Router::get('/admin/stock-management', [PanelController::class, 'adminStockManagement']);
Router::get('/admin/shops-management', [PanelController::class, 'adminShopsManagement']);
Router::get('/admin/users-management', [PanelController::class, 'adminUsersManagement']);
Router::get('/admin/sales-reports', [PanelController::class, 'adminSalesReports']);

Router::get('/staff/billing', [PanelController::class, 'staffBilling']);
Router::get('/staff/today-sales', [PanelController::class, 'staffTodaySales']);
Router::get('/staff/bill-history', [PanelController::class, 'staffBillHistory']);

Router::get('/branch/billing', [PanelController::class, 'branchBilling']);
Router::get('/branch/sales-summary', [PanelController::class, 'branchSalesSummary']);

Router::get('/health', static fn (): string => 'OK');

Router::get('/api/products', [ProductController::class, 'jsonIndex']);
Router::get('/api/products/scan/{id}', [ProductController::class, 'scanner']);
Router::get('/products/{id}/barcode', [ProductController::class, 'barcode']);
Router::get('/api/orders/mode/{mode}/status/{status}', [OrderController::class, 'filter']);
Router::get('/api/orders/counts', [OrderController::class, 'countSummary']);
Router::get('/api/users/operators/{type}', [UserController::class, 'operators']);
Router::get('/api/users/profile/{id}', [UserController::class, 'operatorProfile']);

Router::resource('categories', CategoryController::class);
Router::resource('products', ProductController::class);
Router::resource('orders', OrderController::class);
Router::resource('users', UserController::class);
Router::resource('branches', BranchController::class);

Router::post('/orders/{id}/pay', [OrderController::class, 'pay']);
Router::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
