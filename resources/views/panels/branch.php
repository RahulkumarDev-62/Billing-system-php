<section>
    <h1>Branch Panel</h1>
    <p class="muted">Track stock and sales for the current branch.</p>

    <div class="toolbar">
        <a class="btn" href="<?php echo e(url('/orders/create')); ?>">New Sale</a>
        <a class="btn" href="<?php echo e(url('/products')); ?>">Branch Stock</a>
    </div>

    <div class="card">
        <h3>Branch stock</h3>
        <table>
            <thead>
                <tr><th>Name</th><th>Barcode</th><th>Stock</th><th>Reorder</th></tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo e((string) $product['name']); ?></td>
                        <td><?php echo e((string) $product['barcode']); ?></td>
                        <td><?php echo e((string) $product['stock']); ?></td>
                        <td><?php echo e((string) ($product['reorder_level'] ?? '')); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card" style="margin-top: 18px;">
        <h3>Recent sales</h3>
        <table>
            <thead>
                <tr><th>Sale #</th><th>Customer</th><th>Payment</th><th>Status</th><th>Total</th></tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td><?php echo e((string) $sale['order_no']); ?></td>
                        <td><?php echo e((string) $sale['customer_name']); ?></td>
                        <td><?php echo e((string) $sale['payment_mode']); ?></td>
                        <td><?php echo e((string) $sale['order_status']); ?></td>
                        <td><?php echo e((string) $sale['total']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>