<section>
    <div class="toolbar">
        <h1 style="margin: 0;">Sales</h1>
        <a class="btn" href="<?php echo e(url('/orders/create')); ?>">Create sale</a>
    </div>

    <div class="card">
        <?php if (!empty($orders)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Sale #</th>
                        <th>Receipt</th>
                        <th>Customer</th>
                        <th>Branch</th>
                        <th>Mode</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo e((string) $order['order_no']); ?></td>
                            <td><?php echo e((string) ($order['receipt_no'] ?? '')); ?></td>
                            <td><?php echo e((string) $order['customer_name']); ?></td>
                            <td><?php echo e((string) ($order['branch_name'] ?? $order['branch_id'] ?? '')); ?></td>
                            <td><?php echo e((string) $order['payment_mode']); ?></td>
                            <td><?php echo e((string) $order['order_status']); ?></td>
                            <td><?php echo e((string) $order['total']); ?></td>
                            <td><a class="btn secondary" href="<?php echo e(url('/orders/' . $order['id'])); ?>">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="muted">No orders found.</p>
        <?php endif; ?>
    </div>
</section>