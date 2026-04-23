<section>
    <h1><?php echo e((string) $product['name']); ?> Barcode</h1>
    <div class="card" style="text-align:center;">
        <p class="muted">Scan code</p>
        <div style="display:flex; justify-content:center; gap:2px; align-items:flex-end; margin:20px 0; min-height:120px;">
            <?php foreach (str_split((string) $product['barcode']) as $digit): ?>
                <span style="display:inline-block; width:3px; height:<?php echo e((string) (40 + ((int) $digit * 6))); ?>px; background:#17202a;"></span>
                <span style="display:inline-block; width:1px;"></span>
            <?php endforeach; ?>
        </div>
        <h2 style="letter-spacing:0.3em;"><?php echo e((string) $product['barcode']); ?></h2>
        <p class="muted">Branch ID: <?php echo e((string) ($product['branch_id'] ?? '')); ?> | Stock: <?php echo e((string) ($product['stock'] ?? '')); ?></p>
    </div>
</section>