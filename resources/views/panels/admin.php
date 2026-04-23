<section>
    <h1>Admin Panel</h1>
    <p class="muted">Manage branches, accounts, inventory, and reports.</p>

    <div class="grid" style="margin-bottom: 20px;">
        <div class="card"><h3><?php echo e((string) $stats['users']); ?></h3><p class="muted">Accounts</p></div>
        <div class="card"><h3><?php echo e((string) $stats['branches']); ?></h3><p class="muted">Branches</p></div>
        <div class="card"><h3><?php echo e((string) $stats['categories']); ?></h3><p class="muted">Categories</p></div>
        <div class="card"><h3><?php echo e((string) $stats['products']); ?></h3><p class="muted">Products</p></div>
        <div class="card"><h3><?php echo e((string) $stats['sales']); ?></h3><p class="muted">Sales</p></div>
    </div>

    <div class="toolbar">
        <a class="btn" href="<?php echo e(url('/admin/products')); ?>">Products</a>
        <a class="btn" href="<?php echo e(url('/admin/barcode-generator')); ?>">Barcode Generator</a>
        <a class="btn" href="<?php echo e(url('/admin/stock-management')); ?>">Stock Management</a>
        <a class="btn" href="<?php echo e(url('/admin/shops-management')); ?>">Shops Management</a>
        <a class="btn" href="<?php echo e(url('/admin/users-management')); ?>">Users Management</a>
        <a class="btn" href="<?php echo e(url('/admin/sales-reports')); ?>">Sales Reports</a>
    </div>

    <div class="card">
        <h3>Branches</h3>
        <table>
            <thead>
                <tr><th>Name</th><th>Code</th><th>Phone</th></tr>
            </thead>
            <tbody>
                <?php foreach ($branches as $branch): ?>
                    <tr>
                        <td><?php echo e((string) $branch['name']); ?></td>
                        <td><?php echo e((string) $branch['code']); ?></td>
                        <td><?php echo e((string) ($branch['phone'] ?? '')); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card" style="margin-top: 18px;">
        <h3>Shop Staff and Branch Accounts</h3>
        <table>
            <thead>
                <tr><th>Name</th><th>Email</th><th>Role</th><th>Branch ID</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $account): ?>
                    <tr>
                        <td><?php echo e((string) $account['name']); ?></td>
                        <td><?php echo e((string) $account['email']); ?></td>
                        <td><?php echo e((string) $account['role']); ?></td>
                        <td><?php echo e((string) ($account['branch_id'] ?? '')); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>