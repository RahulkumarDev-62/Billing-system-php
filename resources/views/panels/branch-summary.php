<section>
    <h1>Branch Sales Summary</h1>
    <p class="muted">Latest branch billing summary.</p>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Bill #</th>
                    <th>Receipt</th>
                    <th>Customer</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td><?php echo e((string) $sale['order_no']); ?></td>
                        <td><?php echo e((string) $sale['receipt_no']); ?></td>
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
