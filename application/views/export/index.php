<?php
$module_title = 'Export';
?>
<div id="content">
    <div class="container">
        <h3>Export Data</h3>
        <p>Select a table and format to download:</p>
        <ul>
        <?php foreach ($tables as $t): ?>
            <li><?php echo $t ?> - <a href="<?php echo site_url('export/download/'.$t.'/json') ?>">JSON</a> | <a href="<?php echo site_url('export/download/'.$t.'/csv') ?>">CSV</a> | <a href="<?php echo site_url('export/download/'.$t.'/xlsx') ?>">XLSX</a></li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>
