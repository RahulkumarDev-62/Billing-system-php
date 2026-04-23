<section>
    <h1>Barcode Generator</h1>
    <p class="muted">Each product has one fixed barcode. Open printable labels below.</p>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Barcode</th>
                    <th>Label</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo e((string) $product['name']); ?></td>
                        <td><?php echo e((string) $product['barcode']); ?></td>
                        <td><a class="btn secondary" href="<?php echo e(url('/products/' . $product['id'] . '/barcode')); ?>">Print Label</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
