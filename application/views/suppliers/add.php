<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container">
    <h2>Add Supplier</h2>
    <form method="post" action="<?php echo site_url('suppliers/add'); ?>">
        <div class="form-group"><label>Name</label><input name="name" class="form-control" required></div>
        <div class="form-group"><label>Contact</label><input name="contact" class="form-control"></div>
        <div class="form-group"><label>Phone</label><input name="phone" class="form-control"></div>
        <div class="form-group"><label>Email</label><input name="email" class="form-control"></div>
        <div class="form-group"><label>Notes</label><textarea name="notes" class="form-control"></textarea></div>
        <button class="btn btn-primary">Save</button>
    </form>
</div>
