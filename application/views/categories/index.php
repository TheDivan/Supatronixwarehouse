<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container">
    <h2>Categories</h2>
    <?php if (!empty($this->session->flashdata('message'))): ?>
        <div class="alert alert-info"><?php echo $this->session->flashdata('message'); ?></div>
    <?php endif; ?>
    <ul>
        <?php foreach ($categories as $c): ?>
            <li>
                <?php echo htmlspecialchars($c['name']); ?>
                <?php if (!empty($this->session->userdata('is_admin'))): ?>
                    <?php if (empty($c['is_default'])): ?>
                        <a href="<?php echo site_url('categories/delete/'.$c['id']); ?>" class="btn btn-xs btn-danger" style="margin-left:8px;">Delete</a>
                    <?php else: ?>
                        <span class="badge badge-secondary" style="margin-left:8px;">Default</span>
                    <?php endif; ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php if (!empty($this->session->userdata('is_admin'))): ?>
        <a class="btn btn-primary" href="<?php echo site_url('categories/add'); ?>">Add Category</a>
    <?php endif; ?>
</div>
