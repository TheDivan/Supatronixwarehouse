<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container">
    <h2>Stock Movements</h2>
    <table class="table table-striped">
        <thead>
            <tr><th>ID</th><th>Stock ID</th><th>Change</th><th>Note</th><th>By</th><th>Date</th></tr>
        </thead>
        <tbody>
        <?php foreach ($movements as $m): ?>
            <tr>
                <td><?php echo $m['id']; ?></td>
                <td><?php echo $m['stock_id']; ?></td>
                <td><?php echo $m['change']; ?></td>
                <td><?php echo htmlspecialchars($m['note']); ?></td>
                <td><?php echo $m['created_by']; ?></td>
                <td><?php echo $m['created_datetime']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
