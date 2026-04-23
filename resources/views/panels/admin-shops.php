<section>
    <h1>Shops Management</h1>
    <p class="muted">Create and manage branch shops.</p>
    <div class="toolbar">
        <a class="btn" href="<?php echo e(url('/branches')); ?>">Open Shops Manager</a>
        <a class="btn secondary" href="<?php echo e(url('/branches/create')); ?>">Add Shop</a>
    </div>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Address</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($branches as $branch): ?>
                    <tr>
                        <td><?php echo e((string) $branch['name']); ?></td>
                        <td><?php echo e((string) $branch['code']); ?></td>
                        <td><?php echo e((string) $branch['address']); ?></td>
                        <td><?php echo e((string) $branch['phone']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
