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
                <option value="">All stores</option>
                <?php if (!empty($office_names) && is_array($office_names)): foreach ($office_names as $id => $name):
                    $sel = (!empty($active_office) && (int)$active_office === (int)$id) ? ' selected' : '';
                ?>
                    <option value="<?php echo (int)$id; ?>" data-name="<?php echo htmlspecialchars($name); ?>"<?php echo $sel; ?>><?php echo htmlspecialchars($name); ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select id="supplier-filter" class="form-control">
                <option value="">All suppliers</option>
                <?php if (!empty($suppliers) && is_array($suppliers)): foreach ($suppliers as $sp):
                    $ssel = (!empty($active_supplier) && (int)$active_supplier === (int)$sp['id']) ? ' selected' : '';
                ?>
                    <option value="<?php echo (int)$sp['id']; ?>"<?php echo $ssel; ?>><?php echo htmlspecialchars($sp['name']); ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>
    </div>

    <?php
        // Group stocks by category for display
        $grouped = array();
        foreach ($stocks as $s) {
            $cat = $s['part_category'] ?? 'Uncategorized';
            if (!isset($grouped[$cat])) $grouped[$cat] = array('rows'=>array(),'total_value'=>0,'total_qty'=>0);
            $grouped[$cat]['rows'][] = $s;
            $grouped[$cat]['total_value'] += ((float)($s['cost'] ?? 0)) * ((int)$s['quantity']);
            $grouped[$cat]['total_qty'] += (int)$s['quantity'];
        }
    ?>

    <?php foreach ($grouped as $category => $g): ?>
        <div class="card mb-2">
            <div class="card-header" style="cursor:pointer;" data-toggle="collapse" data-target="#cat-<?php echo md5($category); ?>">
                <strong><?php echo htmlspecialchars($category); ?></strong>
                <span style="float:right;">Total Qty: <?php echo (int)$g['total_qty']; ?> — Total Value: N$<?php echo number_format($g['total_value'],2); ?></span>
            </div>
            <div id="cat-<?php echo md5($category); ?>" class="collapse show">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr><th>Item Name</th><th>Device Model</th><th>Supplier</th><th>Store</th><th>Qty</th><th>Unit Price</th><th>Date Added</th><?php if (!empty($this->session->userdata('is_admin'))): ?><th>Actions</th><?php endif; ?></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($g['rows'] as $s): ?>
                        <tr data-id="<?php echo (int)($s['id'] ?? 0); ?>" data-office="<?php echo $s['office_id'] ?? 0; ?>" data-supplier="<?php echo $s['supplier_id'] ?? ''; ?>">
                            <td><?php echo htmlspecialchars($s['part_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($s['device_model'] ?? ''); ?></td>
                            <td><?php echo !empty($s['supplier'])?htmlspecialchars($s['supplier']):''; ?></td>
                            <td><?php echo htmlspecialchars($office_names[$s['office_id'] ?? 0] ?? ''); ?></td>
                            <td><?php $q=(int)($s['quantity']??0); if ($q < 3) echo '<span class="badge badge-danger">'.$q.'</span>'; else echo $q; ?></td>
                            <td><?php echo isset($s['cost']) ? 'N$'.number_format($s['cost'],2) : ''; ?></td>
                            <td><?php echo htmlspecialchars($s['created_datetime'] ?? ''); ?></td>
                            <?php if (!empty($this->session->userdata('is_admin'))): ?>
                                <td>
                                    <a class="btn btn-xs btn-danger stock-delete" href="<?php echo site_url('stock/delete/'.(int)($s['id'] ?? 0)); ?>">Delete</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
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
                    var savedId = <?php echo (int)($saved['id'] ?? 0); ?>;
                    var shownKey = 'saved_shown_' + savedId;
                    // Do not re-show if already shown for this saved id
                    if (savedId && localStorage.getItem(shownKey)) return;
                    if (typeof jQuery !== 'undefined' && jQuery().modal) {
                        jQuery('#savedModal').modal('show');
                        jQuery('#savedModalOk').on('click', function(){ try { localStorage.setItem(shownKey, '1'); } catch(e){} });
                    } else {
                        // fallback to simple alert if Bootstrap modal not available
                        var ok = confirm('Saved stock item: ' + (<?php echo json_encode($saved['part_name'] ?? ''); ?>) + '\nOK to continue');
                        try { if (savedId) localStorage.setItem(shownKey, '1'); } catch(e){}
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
    // When office filter changes, reload page with office_id parameter
    var officeSel = document.getElementById('office-filter');
    if (officeSel) {
        officeSel.addEventListener('change', function(){
            var params = new URLSearchParams(window.location.search);
            if (this.value === '') {
                params.delete('office_id');
            } else {
                params.set('office_id', this.value);
            }
            window.location.search = params.toString();
        });
    }
    var supplierSel = document.getElementById('supplier-filter');
    if (supplierSel) {
        supplierSel.addEventListener('change', function(){
            var params = new URLSearchParams(window.location.search);
            if (this.value === '') {
                params.delete('supplier_id');
            } else {
                params.set('supplier_id', this.value);
            }
            window.location.search = params.toString();
        });
    }
    // confirmation for admin delete buttons
    Array.prototype.forEach.call(document.querySelectorAll('.stock-delete'), function(el){
        el.addEventListener('click', function(e){
            if (!confirm('Delete this stock item? This cannot be undone.')) {
                e.preventDefault();
                return false;
            }
            return true;
        });
    });
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
