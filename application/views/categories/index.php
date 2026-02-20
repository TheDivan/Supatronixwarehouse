<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container">
    <h2>Categories</h2>
    <?php if (!empty($this->session->flashdata('message'))): ?>
        <div class="alert alert-info"><?php echo $this->session->flashdata('message'); ?></div>
    <?php endif; ?>
    <ul>
        <?php foreach ($categories as $c): ?>
            <li><?php echo htmlspecialchars($c['name']); ?></li>
        <?php endforeach; ?>
    </ul>
    <?php if (!empty($this->session->userdata('is_admin'))): ?>
        <a class="btn btn-primary" href="<?php echo site_url('categories/add'); ?>">Add Category</a>
    <?php endif; ?>
</div>
