<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container">
    <h2>Add Category</h2>
    <form method="post" action="<?php echo site_url('categories/add'); ?>">
        <div class="form-group">
            <label>Category Name</label>
            <input name="name" class="form-control" required />
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
</div>
