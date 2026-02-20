<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container">
    <h2>Edit Stock Item</h2>
    <form method="post" action="<?php echo site_url('stock/edit/'.$item['id']); ?>">
        <div class="form-group">
            <label>Category</label>
            <input name="part_category" class="form-control" value="<?php echo htmlspecialchars($item['part_category']); ?>" />
        </div>
        <div class="form-group">
            <label>Part Name</label>
            <input name="part_name" class="form-control" required value="<?php echo htmlspecialchars($item['part_name']); ?>" />
        </div>
        <div class="form-group">
            <label>Quantity</label>
            <input name="quantity" type="number" class="form-control" value="<?php echo (int)$item['quantity']; ?>" />
        </div>
        <div class="form-group">
            <label>Cost</label>
            <input name="cost" type="text" class="form-control" value="<?php echo $item['cost']; ?>" />
        </div>
        <div class="form-group">
            <label>Supplier</label>
            <input name="supplier" class="form-control" value="<?php echo htmlspecialchars($item['supplier']); ?>" />
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control"><?php echo htmlspecialchars($item['notes']); ?></textarea>
        </div>
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-danger" href="<?php echo site_url('stock/delete/'.$item['id']); ?>" onclick="return confirm('Delete this stock item?');">Delete</a>
    </form>
</div>
