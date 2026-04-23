<section>
    <h1>Stock Management</h1>
    <p class="muted">Low stock items based on reorder level.</p>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Barcode</th>
                    <th>Stock</th>
                    <th>Reorder Level</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lowStock as $item): ?>
                    <tr>
                        <td><?php echo e((string) $item['name']); ?></td>
                        <td><?php echo e((string) $item['barcode']); ?></td>
                        <td><?php echo e((string) $item['stock']); ?></td>
                        <td><?php echo e((string) $item['reorder_level']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
