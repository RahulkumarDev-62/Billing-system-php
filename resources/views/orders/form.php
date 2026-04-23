<?php
$item = $item ?? [];
$canSelectBranch = $canSelectBranch ?? false;
?>
<section>
    <h1><?php echo e((string) $title); ?></h1>
    <div class="card">
        <form method="post" action="<?php echo e(url($action)); ?>">
            <?php echo csrf_field(); ?>
            <div class="grid">
                <div class="field">
                    <label>Receipt No</label>
                    <input type="text" name="receipt_no" value="<?php echo e((string) ($item['receipt_no'] ?? '')); ?>" placeholder="Optional receipt number">
                </div>
                <div class="field">
                    <label>Customer name</label>
                    <input type="text" name="customer_name" value="<?php echo e((string) ($item['customer_name'] ?? '')); ?>">
                </div>
                <div class="field">
                    <label>Customer mobile</label>
                    <input type="text" name="customer_mobile" value="<?php echo e((string) ($item['customer_mobile'] ?? '')); ?>">
                </div>
                <div class="field">
                    <label>Customer email</label>
                    <input type="email" name="customer_email" value="<?php echo e((string) ($item['customer_email'] ?? '')); ?>">
                </div>
                <div class="field">
                    <label>Payment mode</label>
                    <select name="payment_mode">
                        <option value="cash">Cash</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <?php if ($canSelectBranch): ?>
                    <div class="field">
                        <label>Branch</label>
                        <select name="branch_id">
                            <?php foreach (($branches ?? []) as $branch): ?>
                                <option value="<?php echo e((string) $branch['id']); ?>" <?php echo ((string) ($selectedBranchId ?? '') === (string) $branch['id']) ? 'selected' : ''; ?>>
                                    <?php echo e((string) $branch['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="branch_id" value="<?php echo e((string) ($selectedBranchId ?? '')); ?>">
                <?php endif; ?>
            </div>

            <div class="card" style="margin-top: 18px;">
                <h3>Items (Scan barcode or enter manually)</h3>
                <?php if (!empty($errors['items'])): ?><div class="error-text"><?php echo e((string) $errors['items']); ?></div><?php endif; ?>
                <div id="order-lines">
                    <div class="grid order-line">
                        <div class="field">
                            <label>Barcode</label>
                            <input type="text" name="items[0][barcode]" placeholder="Scan barcode">
                        </div>
                        <div class="field">
                            <label>Quantity</label>
                            <input type="number" name="items[0][quantity]" min="1" value="1">
                        </div>
                        <div class="field">
                            <label>Discount %</label>
                            <input type="number" name="items[0][discount_percent]" min="0" step="0.01" value="0">
                        </div>
                    </div>
                </div>
                <p class="muted" id="line-count" style="margin-top:10px;">Cart Lines: 1</p>
                <button class="btn secondary" type="button" onclick="addLine()">Add line</button>
            </div>

            <div style="margin-top: 18px;">
                <button class="btn" type="submit"><?php echo e((string) $submitLabel); ?></button>
            </div>
        </form>
    </div>

    <script>
        let lineIndex = 1;
        function addLine() {
            const wrapper = document.getElementById('order-lines');
            const line = document.createElement('div');
            line.className = 'grid order-line';
            line.innerHTML = `
                <div class="field">
                    <label>Barcode</label>
                    <input type="text" name="items[${lineIndex}][barcode]" placeholder="Scan barcode">
                </div>
                <div class="field">
                    <label>Quantity</label>
                    <input type="number" name="items[${lineIndex}][quantity]" min="1" value="1">
                </div>
                <div class="field">
                    <label>Discount %</label>
                    <input type="number" name="items[${lineIndex}][discount_percent]" min="0" step="0.01" value="0">
                </div>`;
            wrapper.appendChild(line);
            lineIndex++;
            document.getElementById('line-count').textContent = `Cart Lines: ${lineIndex}`;
        }
    </script>
</section>