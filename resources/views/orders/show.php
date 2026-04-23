<section>
    <h1>Sale #<?php echo e((string) $order['order_no']); ?></h1>
    <div class="card">
        <p><strong>Customer:</strong> <?php echo e((string) $order['customer_name']); ?></p>
        <p><strong>Mobile:</strong> <?php echo e((string) $order['customer_mobile']); ?></p>
        <p><strong>Email:</strong> <?php echo e((string) $order['customer_email']); ?></p>
        <p><strong>Status:</strong> <?php echo e((string) $order['order_status']); ?></p>
        <p><strong>Payment:</strong> <?php echo e((string) $order['payment_mode']); ?></p>
        <p><strong>Receipt:</strong> <?php echo e((string) ($order['receipt_no'] ?? '')); ?></p>
        <p><strong>Total:</strong> <?php echo e((string) $order['total']); ?></p>

        <h3>Items</h3>
        <table>
            <thead>
                <tr>
                    <th>Barcode</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Discount %</th>
                    <th>Line Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($details['items'] ?? []) as $item): ?>
                    <tr>
                        <td><?php echo e((string) ($item['barcode'] ?? '')); ?></td>
                        <td><?php echo e((string) ($item['product_name'] ?? '')); ?></td>
                        <td><?php echo e((string) ($item['quantity'] ?? '')); ?></td>
                        <td><?php echo e((string) ($item['price'] ?? '')); ?></td>
                        <td><?php echo e((string) ($item['discount_percent'] ?? '')); ?></td>
                        <td><?php echo e((string) ($item['line_total'] ?? '')); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (($order['order_status'] ?? '') === 'pending'): ?>
            <form method="post" action="<?php echo e(url('/orders/' . $order['id'] . '/pay')); ?>" style="margin-top: 16px; display:inline;">
                <?php echo csrf_field(); ?>
                <button class="btn" type="submit">Confirm payment</button>
            </form>
        <?php endif; ?>

        <?php if (auth_is_admin() && ($order['order_status'] ?? '') !== 'cancelled'): ?>
            <form method="post" action="<?php echo e(url('/orders/' . $order['id'] . '/cancel')); ?>" style="margin-top: 16px; display:inline;">
                <?php echo csrf_field(); ?>
                <button class="btn secondary" type="submit">Cancel order</button>
            </form>
        <?php endif; ?>
    </div>
</section>