<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container">
    <h2>Edit API Token</h2>
    <form method="post" action="<?php echo site_url('api_tokens/edit/'.$token['id']); ?>">
        <div class="form-group"><label>Name</label><input name="name" class="form-control" value="<?php echo htmlspecialchars($token['name']); ?>"></div>
        <div class="form-group"><label>Employee ID</label><input name="employee_id" type="number" class="form-control" value="<?php echo $token['employee_id']; ?>"></div>
        <div class="form-group"><label>Is Admin</label><input name="is_admin" type="checkbox" value="1" <?php echo $token['is_admin'] ? 'checked' : ''; ?>></div>
        <div class="form-group"><label>Expires (YYYY-MM-DD HH:MM:SS)</label><input name="expires" class="form-control" value="<?php echo htmlspecialchars($token['expires']); ?>"></div>
        <div class="form-group"><label>Current Token (not editable)</label><div><code><?php echo htmlspecialchars($token['token']); ?></code></div></div>
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-warning" href="<?php echo site_url('api_tokens/regenerate/'.$token['id']); ?>" onclick="return confirm('Regenerate token? This will invalidate the old token.');">Regenerate Token</a>
    </form>
</div>
