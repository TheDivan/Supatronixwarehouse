<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container">
    <h2>Seed Report</h2>
    <?php if (empty($inserted)): ?>
        <p>No new rows were inserted (all defaults already present).</p>
    <?php else: ?>
        <p>Inserted the following default parts:</p>
        <table class="table table-striped">
            <thead><tr><th>ID</th><th>Part Name</th><th>Office</th></tr></thead>
            <tbody>
            <?php foreach ($inserted as $r): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['id']); ?></td>
                    <td><?php echo htmlspecialchars($r['part_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['office_id']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
