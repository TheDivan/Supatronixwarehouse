<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container">
    <h2>Suppliers</h2>
    <?php if ($this->is_admin): ?><p><a href="<?php echo site_url('suppliers/add'); ?>" class="btn btn-primary">Add Supplier</a></p><?php endif; ?>
    <table class="table table-striped">
        <thead><tr><th>ID</th><th>Name</th><th>Contact</th><th>Phone</th><th>Email</th><th>Notes</th><?php if ($this->is_admin): ?><th>Actions</th><?php endif; ?></tr></thead>
        <tbody>
        <?php foreach ($suppliers as $s): ?>
            <tr>
                <td><?php echo $s['id']; ?></td>
                <td><?php echo htmlspecialchars($s['name']); ?></td>
                <td><?php echo htmlspecialchars($s['contact']); ?></td>
                <td><?php echo htmlspecialchars($s['phone']); ?></td>
                <td><?php echo htmlspecialchars($s['email']); ?></td>
                <td><?php echo htmlspecialchars($s['notes']); ?></td>
                <?php if ($this->is_admin): ?><td><a href="<?php echo site_url('suppliers/edit/'.$s['id']); ?>">Edit</a> | <a href="<?php echo site_url('suppliers/delete/'.$s['id']); ?>" onclick="return confirm('Delete supplier?');">Delete</a></td><?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
