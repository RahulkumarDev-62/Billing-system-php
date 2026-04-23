<?php
$title = $title ?? 'Supermarket Management System';
$siteName = config('app.name', 'Supermarket Management System');
$user = auth_user();
$role = auth_role();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title); ?> | <?php echo e((string) $siteName); ?></title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f2f6ff;
            --panel: #ffffff;
            --text: #101828;
            --muted: #5b6572;
            --accent: #1E4AFF;
            --border: #dbe4ff;
            --ring: #93c5fd;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(180deg, #f2f7ff 0%, #e8efff 100%);
            color: var(--text);
        }
        .shell {
            max-width: 1100px;
            margin: 0 auto;
            padding: 32px 20px 48px;
        }
        .topbar,
        .panel {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(23, 32, 42, 0.08);
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 22px;
            margin-bottom: 24px;
            gap: 18px;
        }
        .brand-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-wrap img {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid #bfdbfe;
        }
        .brand {
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--accent);
        }
        .nav {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .nav a {
            color: var(--text);
            text-decoration: none;
            font-size: 14px;
            border: 1px solid transparent;
            padding: 6px 10px;
            border-radius: 9px;
            transition: 0.18s ease;
        }
        .nav a:hover {
            border-color: var(--ring);
            background: #eff6ff;
        }
        .menu-toggle {
            display: none;
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 8px;
            padding: 8px 10px;
            font: inherit;
            cursor: pointer;
        }
        .banner {
            margin-bottom: 18px;
            padding: 12px 16px;
            border-radius: 12px;
            background: #e6f2eb;
            border: 1px solid #bfd7c8;
        }
        .banner.error {
            background: #fae9e6;
            border-color: #e4bbb1;
        }
        .panel {
            padding: 28px;
        }
        h1, h2, h3, p { margin-top: 0; }
        .muted { color: var(--muted); }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }
        .card {
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px;
            background: #fffdf9;
        }
        code {
            display: inline-block;
            background: #f3e7db;
            padding: 2px 6px;
            border-radius: 6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px 8px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            vertical-align: top;
        }
        .actions a,
        .actions button {
            margin-right: 8px;
        }
        .btn {
            display: inline-block;
            border: 1px solid var(--accent);
            background: var(--accent);
            color: #fff;
            border-radius: 10px;
            padding: 10px 14px;
            text-decoration: none;
            cursor: pointer;
        }
        .btn.secondary {
            background: transparent;
            color: var(--accent);
        }
        .field {
            margin-bottom: 16px;
        }
        .field label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .field input,
        .field select,
        .field textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font: inherit;
        }
        .error-text {
            color: #b03a2e;
            font-size: 13px;
            margin-top: 5px;
        }
        .toolbar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }
        @media (max-width: 900px) {
            .topbar {
                flex-wrap: wrap;
            }
            .menu-toggle {
                display: inline-block;
            }
            .nav {
                display: none;
                width: 100%;
                padding-top: 8px;
                border-top: 1px solid var(--border);
            }
            .nav.open {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div class="brand-wrap">
                <img src="<?php echo e(url('/assets/logo.svg')); ?>" alt="SMS logo">
                <div>
                    <div class="brand"><?php echo e((string) $siteName); ?></div>
                    <div class="muted">Panel-based supermarket operations</div>
                </div>
            </div>
            <button class="menu-toggle" id="menuToggle" type="button" aria-label="Toggle menu">Menu</button>
            <nav class="nav" id="mainNav">
                <?php if ($user): ?>
                    <?php if ($role === 'admin'): ?>
                        <a href="<?php echo e(url('/admin/dashboard')); ?>">Dashboard</a>
                        <a href="<?php echo e(url('/admin/products')); ?>">Products</a>
                        <a href="<?php echo e(url('/admin/barcode-generator')); ?>">Barcode Generator</a>
                        <a href="<?php echo e(url('/admin/stock-management')); ?>">Stock</a>
                        <a href="<?php echo e(url('/admin/shops-management')); ?>">Shops</a>
                        <a href="<?php echo e(url('/admin/users-management')); ?>">Users</a>
                        <a href="<?php echo e(url('/admin/sales-reports')); ?>">Reports</a>
                    <?php elseif ($role === 'staff'): ?>
                        <a href="<?php echo e(url('/staff/billing')); ?>">Billing</a>
                        <a href="<?php echo e(url('/staff/today-sales')); ?>">Today Sales</a>
                        <a href="<?php echo e(url('/staff/bill-history')); ?>">Bill History</a>
                    <?php elseif ($role === 'branch'): ?>
                        <a href="<?php echo e(url('/branch/billing')); ?>">Billing</a>
                        <a href="<?php echo e(url('/branch/sales-summary')); ?>">Sales Summary</a>
                    <?php endif; ?>
                    <a href="<?php echo e(url('/logout')); ?>">Logout</a>
                <?php else: ?>
                    <a href="<?php echo e(url('/admin-login')); ?>">Admin Login</a>
                    <a href="<?php echo e(url('/staff-login')); ?>">Staff Login</a>
                    <a href="<?php echo e(url('/branch-login')); ?>">Branch Login</a>
                <?php endif; ?>
            </nav>
        </header>

        <?php if ($message = flash('success')): ?>
            <div class="banner"><?php echo e((string) $message); ?></div>
        <?php endif; ?>

        <?php if ($message = flash('error')): ?>
            <div class="banner error"><?php echo e((string) $message); ?></div>
        <?php endif; ?>

        <main class="panel">
            <?php echo $content; ?>
        </main>
    </div>
    <script>
        (function () {
            const toggle = document.getElementById('menuToggle');
            const nav = document.getElementById('mainNav');
            if (!toggle || !nav) {
                return;
            }
            toggle.addEventListener('click', function () {
                nav.classList.toggle('open');
            });
        })();
    </script>
</body>
</html>
