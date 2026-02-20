<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container">
    <h2 class="section-header">Stock Items</h2>
    <div class="mb-2">
        <a href="<?php echo site_url('stock/add'); ?>" class="btn btn-primary">Add Stock Item</a>
        <?php if (!empty(
            $this->session->userdata('is_admin')
        )): ?>
            <a href="<?php echo site_url('stock/seed_defaults'); ?>" class="btn btn-warning">Seed Default Parts</a>
        <?php endif; ?>
    </div>
    <?php
        // build a small office index for client-side filtering
        $offmap = array();
        foreach ($stocks as $s) {
            $oid = $s['office_id'] ?? 0;
            $offmap[$oid] = isset($offmap[$oid]) ? $offmap[$oid] + 1 : 1;
        }
    ?>
    <div class="row mb-2">
        <div class="col-md-4"><input id="stock-search" class="form-control" placeholder="Search stock..." /></div>
        <div class="col-md-3">
            <select id="office-filter" class="form-control">
                <option value="">All offices</option>
                <?php
                    // Render office filter options server-side with friendly names and abbreviations
                    $abbrev = array(1 => 'WB', 2 => 'SWK');
                    foreach ($offmap as $oid => $count) {
                        if ($oid === '' || $oid === null) continue;
                        $name = isset($office_names[$oid]) ? $office_names[$oid] : ('Office ' . $oid);
                        $abbr = isset($abbrev[$oid]) ? $abbrev[$oid] : '';
                        $label = $name . ($abbr ? ' (' . $abbr . ')' : '') . ' (' . $count . ')';
                        echo '<option value="' . $oid . '" data-name="' . htmlspecialchars($name) . '">' . htmlspecialchars($label) . '</option>';
                    }
                ?>
            </select>
        </div>
    </div>
    <table id="stock-table" class="table table-striped">
        <thead>
            <tr>
                <th>Category</th>
                <th>Office</th>
                <th>Part</th>
                <th>Qty</th>
                <th>Cost</th>
                <th>Supplier</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
        <?php
            // build a small office index for client-side filtering
            $offmap = array();
            foreach ($stocks as $s) {
                $oid = $s['office_id'] ?? 0;
                $offmap[$oid] = isset($offmap[$oid]) ? $offmap[$oid] + 1 : 1;
            }
        ?>
        
        <?php foreach ($stocks as $s): ?>
            <tr data-office="<?php echo $s['office_id'] ?? 0; ?>">
                <td>
                    <?php $cat = htmlspecialchars((string)($s['part_category'] ?? '')); ?>
                    <a href="<?php echo site_url('stock?category=' . urlencode($s['part_category'])); ?>"><?php echo $cat; ?></a>
                </td>
                <td><?php echo htmlspecialchars((string)($office_names[$s['office_id'] ?? 0] ?? ('Office ' . ($s['office_id'] ?? 0)))); ?></td>
                <td><?php echo htmlspecialchars((string)($s['part_name'] ?? '')); ?></td>
                <td><?php echo (int)$s['quantity']; ?></td>
                <td><?php echo isset($s['cost']) ? $s['cost'] : ''; ?></td>
                <td><?php if (!empty($s['supplier_id'])) { ?><a href="<?php echo site_url('suppliers/edit/'.$s['supplier_id']); ?>"><?php echo htmlspecialchars((string)$s['supplier']); ?></a><?php } elseif (!empty($s['supplier'])) { echo htmlspecialchars((string)$s['supplier']); } else { echo ''; } ?></td>
                <td><?php echo htmlspecialchars((string)($s['notes'] ?? '')); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
    <?php if (!empty($saved)): ?>
            <div id="savedModal" class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Stock Saved</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Saved stock item: <?php echo htmlspecialchars($saved['part_name'] ?? ''); ?> (ID: <?php echo (int)($saved['id'] ?? 0); ?>)</p>
                            <p>Please click OK to continue.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="savedModalOk" class="btn btn-primary" data-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function(){
                    try {
                            if (typeof jQuery !== 'undefined' && jQuery().modal) {
                                    jQuery('#savedModal').modal('show');
                            } else {
                                    // fallback to simple alert if Bootstrap modal not available
                                    var ok = confirm('Saved stock item: ' + (<?php echo json_encode($saved['part_name'] ?? ''); ?>) + '\nOK to continue');
                            }
                    } catch(e) { console.error(e); }
            });
            </script>
    <?php endif; ?>
<script>
// Initialize DataTables for stock table
document.addEventListener('DOMContentLoaded', function(){
    if (typeof jQuery === 'undefined' || !jQuery().DataTable) return;
    var table = jQuery('#stock-table').DataTable({
        pageLength: 25
    });
    // populate office filter
    var officeSel = document.getElementById('office-filter');
    if (officeSel) {
        // Use the option's data-name attribute (friendly office name) for DataTables search
        officeSel.addEventListener('change', function(){
            var val = this.value;
            if (val === '') {
                table.column(2).search('').draw();
            } else {
                var name = this.options[this.selectedIndex].getAttribute('data-name') || this.options[this.selectedIndex].text;
                table.column(2).search('^' + jQuery.fn.dataTable.util.escapeRegex(name) + '$', true, false).draw();
            }
        });
    }
    // wire up free-text search to DataTables search
    var search = document.getElementById('stock-search');
    if (search) search.addEventListener('input', function(){ table.search(this.value).draw(); });
});
</script>
<script>
// Simple client-side search and office filter for the stock table
document.addEventListener('DOMContentLoaded', function(){
    var search = document.getElementById('stock-search');
    var office = document.getElementById('office-filter');
    var tbody = document.querySelector('table.table tbody');
    function applyFilters(){
        var q = (search && search.value || '').toLowerCase();
        var o = (office && office.value) || '';
        Array.prototype.forEach.call(tbody.querySelectorAll('tr'), function(row){
            var text = row.innerText.toLowerCase();
            var matchesQ = q === '' || text.indexOf(q) !== -1;
            var matchesO = o === '' || row.getAttribute('data-office') === o;
            row.style.display = (matchesQ && matchesO) ? '' : 'none';
        });
    }
    if (search) search.addEventListener('input', applyFilters);
    if (office) office.addEventListener('change', applyFilters);
});
</script>
