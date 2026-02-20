<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container">
    <h2>API Tokens</h2>
    <p><a href="<?php echo site_url('api_tokens/add'); ?>" class="btn btn-primary">Create Token</a></p>
    <table class="table table-striped">
        <thead><tr><th>ID</th><th>Name</th><th>Employee ID</th><th>Token</th><th>Admin</th><th>Expires</th><th>Created</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($tokens as $t): ?>
            <tr>
                <td><?php echo $t['id']; ?></td>
                <td><?php echo htmlspecialchars($t['name']); ?></td>
                <td><?php echo $t['employee_id']; ?></td>
                <td><code><?php echo htmlspecialchars($t['token']); ?></code></td>
                <td><?php echo $t['is_admin'] ? 'Yes' : 'No'; ?></td>
                <td><?php echo $t['expires']; ?></td>
                <td><?php echo $t['created_datetime']; ?></td>
                <td>
                    <a href="<?php echo site_url('api_tokens/edit/'.$t['id']); ?>">Edit</a> |
                    <a href="<?php echo site_url('api_tokens/regenerate/'.$t['id']); ?>" onclick="return confirm('Regenerate token? This will invalidate the old token.');">Regenerate</a> |
                    <a href="<?php echo site_url('api_tokens/delete/'.$t['id']); ?>" onclick="return confirm('Delete token?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
