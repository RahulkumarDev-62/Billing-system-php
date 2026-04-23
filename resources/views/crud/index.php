<section>
    <div class="toolbar">
        <h1 style="margin: 0;"><?php echo e((string) $resourceLabel); ?></h1>
        <?php if (auth_is_admin()): ?>
            <a class="btn" href="<?php echo e(url($basePath . '/create')); ?>">Create new</a>
        <?php endif; ?>
    </div>

    <div class="card">
        <?php if (!empty($items)): ?>
            <table>
                <thead>
                    <tr>
                        <?php foreach ($fields as $field): ?>
                            <th><?php echo e((string) $field['label']); ?></th>
                        <?php endforeach; ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <?php foreach ($fields as $field): ?>
                                <td><?php echo e((string) ($item[$field['name']] ?? '')); ?></td>
                            <?php endforeach; ?>
                            <td class="actions">
                                <?php if (auth_is_admin()): ?>
                                    <a class="btn secondary" href="<?php echo e(url($basePath . '/' . ($item['id'] ?? 0) . '/edit')); ?>">Edit</a>
                                    <form action="<?php echo e(url($basePath . '/' . ($item['id'] ?? 0) . '/delete')); ?>" method="post" style="display:inline;">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn" type="submit">Delete</button>
                                    </form>
                                <?php else: ?>
                                    <span class="muted">Read only</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="muted">No records found.</p>
        <?php endif; ?>
    </div>
</section>
