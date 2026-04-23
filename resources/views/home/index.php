<section>
    <h1>Billing dashboard</h1>
    <p class="muted">Manage sales, categories, products, orders, and user access from one PHP MVC app.</p>

    <div class="grid" style="margin-bottom: 22px;">
        <div class="card"><h3><?php echo e((string) ($stats['users'] ?? 0)); ?></h3><p class="muted">Users</p></div>
        <div class="card"><h3><?php echo e((string) ($stats['categories'] ?? 0)); ?></h3><p class="muted">Categories</p></div>
        <div class="card"><h3><?php echo e((string) ($stats['products'] ?? 0)); ?></h3><p class="muted">Products</p></div>
        <div class="card"><h3><?php echo e((string) ($stats['orders'] ?? 0)); ?></h3><p class="muted">Orders</p></div>
    </div>

    <div class="card">
        <h3>Recent orders</h3>
        <?php if (!empty($recentOrders)): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Mode</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><?php echo e((string) ($order['order_no'] ?? '')); ?></td>
                            <td><?php echo e((string) ($order['customer_name'] ?? '')); ?></td>
                            <td><?php echo e((string) ($order['payment_mode'] ?? '')); ?></td>
                            <td><?php echo e((string) ($order['order_status'] ?? '')); ?></td>
                            <td><?php echo e((string) ($order['total'] ?? '0.00')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="muted">No orders yet.</p>
        <?php endif; ?>
    </div>
</section>
