<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container">
    <h2>Create API Token</h2>
    <form method="post" action="<?php echo site_url('api_tokens/add'); ?>">
        <div class="form-group"><label>Name</label><input name="name" class="form-control"></div>
        <div class="form-group"><label>Employee ID</label><input name="employee_id" type="number" class="form-control" value="1"></div>
        <div class="form-group"><label>Is Admin</label><input name="is_admin" type="checkbox" value="1"></div>
        <div class="form-group"><label>Expires (YYYY-MM-DD HH:MM:SS)</label><input name="expires" class="form-control"></div>
        <button class="btn btn-primary">Create</button>
    </form>
</div>
