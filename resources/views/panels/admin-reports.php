<section>
    <h1>Sales Reports</h1>
    <p class="muted">Daily billing trends for the last 30 days.</p>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Total Bills</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($summary as $row): ?>
                    <tr>
                        <td><?php echo e((string) $row['sale_date']); ?></td>
                        <td><?php echo e((string) $row['bills']); ?></td>
                        <td><?php echo e((string) number_format((float) $row['revenue'], 2)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
