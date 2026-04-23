<section>
    <h1>Users Management</h1>
    <p class="muted">Admin creates and manages staff + branch accounts.</p>
    <div class="toolbar">
        <a class="btn" href="<?php echo e(url('/users')); ?>">Open Accounts Manager</a>
        <a class="btn secondary" href="<?php echo e(url('/users/create')); ?>">Create Account</a>
    </div>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Branch</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo e((string) $user['name']); ?></td>
                        <td><?php echo e((string) $user['email']); ?></td>
                        <td><?php echo e((string) ucfirst($user['role'])); ?></td>
                        <td><?php echo e((string) ($user['branch_name'] ?? 'N/A')); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
