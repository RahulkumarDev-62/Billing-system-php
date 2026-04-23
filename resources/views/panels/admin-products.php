<section>
    <h1>Products</h1>
    <p class="muted">Admin can create and manage all products globally.</p>
    <div class="toolbar">
        <a class="btn" href="<?php echo e(url('/products')); ?>">Open Product Manager</a>
        <a class="btn secondary" href="<?php echo e(url('/products/create')); ?>">Create Product</a>
        <form method="post" action="<?php echo e(url('/products/demo-create')); ?>" style="display:inline;">
            <?php echo csrf_field(); ?>
            <button class="btn" type="submit">Create Demo Products</button>
        </form>
    </div>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Barcode</th>
                    <th>Category</th>
                    <th>Branch</th>
                    <th>Price</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo e((string) ($product['name'] ?? '')); ?></td>
                        <td><?php echo e((string) ($product['barcode'] ?? '')); ?></td>
                        <td><?php echo e((string) ($product['category_name'] ?? '')); ?></td>
                        <td><?php echo e((string) ($product['branch_name'] ?? 'All')); ?></td>
                        <td><?php echo e((string) ($product['price'] ?? '0')); ?></td>
                        <td><?php echo e((string) ($product['stock'] ?? '0')); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
