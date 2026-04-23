<section>
    <h1>Bill History</h1>
    <p class="muted">Recent billing history created by you.</p>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Bill #</th>
                    <th>Receipt</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td><?php echo e((string) $sale['order_no']); ?></td>
                        <td><?php echo e((string) $sale['receipt_no']); ?></td>
                        <td><?php echo e((string) $sale['customer_name']); ?></td>
                        <td><?php echo e((string) $sale['created_at']); ?></td>
                        <td><?php echo e((string) $sale['total']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
