<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container">
    <h2>Edit Supplier</h2>
    <form method="post" action="<?php echo site_url('suppliers/edit/'.$supplier['id']); ?>">
        <div class="form-group"><label>Name</label><input name="name" class="form-control" required value="<?php echo htmlspecialchars($supplier['name']); ?>"></div>
        <div class="form-group"><label>Contact</label><input name="contact" class="form-control" value="<?php echo htmlspecialchars($supplier['contact']); ?>"></div>
        <div class="form-group"><label>Phone</label><input name="phone" class="form-control" value="<?php echo htmlspecialchars($supplier['phone']); ?>"></div>
        <div class="form-group"><label>Email</label><input name="email" class="form-control" value="<?php echo htmlspecialchars($supplier['email']); ?>"></div>
        <div class="form-group"><label>Notes</label><textarea name="notes" class="form-control"><?php echo htmlspecialchars($supplier['notes']); ?></textarea></div>
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-danger" href="<?php echo site_url('suppliers/delete/'.$supplier['id']); ?>" onclick="return confirm('Delete supplier?');">Delete</a>
    </form>
</div>
