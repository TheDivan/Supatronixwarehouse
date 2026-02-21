<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container">
    <h2>Add Stock Item</h2>
    <form method="post" action="<?php echo site_url('stock/add'); ?>">
        <div class="form-group">
            <label>Category</label>
            <select id="part_category" name="part_category" class="form-control" required>
                <option value="">-- select --</option>
                <?php foreach(($categories ?? array()) as $c): ?>
                    <option value="<?php echo htmlspecialchars($c['name']); ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($this->session->userdata('is_admin'))): ?>
                <small><a href="#" id="show-add-category">Add new category</a></small>
            <?php endif; ?>
        </div>
        <div class="form-group" id="add-category-form" style="display:none;">
            <label>New Category</label>
            <div class="input-group">
                <input id="category-add-name" name="name" class="form-control" />
                <span class="input-group-btn"><button id="category-add-submit" type="button" class="btn btn-primary">Add</button></span>
            </div>
        </div>
        <div class="form-group">
            <label>Allocate To (Store Location)</label>
            <select name="office_id" class="form-control">
                <?php if (!empty($store_locations) && is_array($store_locations)): ?>
                    <?php foreach ($store_locations as $loc): ?>
                        <?php if ($this->session->userdata('is_admin') || (int)$this->session->userdata('office_id') === (int)$loc['id']): ?>
                            <option value="<?php echo (int)$loc['id']; ?>"><?php echo htmlspecialchars($loc['name']); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="1">Walvis Bay</option>
                    <option value="2">Swakopmund</option>
                <?php endif; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Part Name</label>
            <select id="part_name" name="part_name" class="form-control">
                <option value="">-- select or type --</option>
            </select>
            <small>Or enter a custom name below</small>
            <input id="part_name_custom" type="text" name="part_name_custom" class="form-control mt-1" placeholder="Custom part name (optional)" />
        </div>
        <div class="form-group">
            <label>Device Model</label>
            <input name="device_model" type="text" class="form-control" placeholder="e.g. Samsung Galaxy A53" required />
        </div>
        <div class="form-group">
            <label>Quantity</label>
            <input name="quantity" type="number" class="form-control" value="1" />
        </div>
        <div class="form-group">
            <label>Cost</label>
            <input name="cost" type="text" class="form-control" />
        </div>
        <div class="form-group">
            <label>Supplier</label>
            <select name="supplier_id" class="form-control" required>
                <option value="">-- select supplier --</option>
                <?php foreach(($suppliers ?? array()) as $sp): ?>
                    <option value="<?php echo $sp['id']; ?>"><?php echo htmlspecialchars($sp['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var cat = document.getElementById('part_category');
    var parts = document.getElementById('part_name');
    var custom = document.getElementById('part_name_custom');
    function loadParts(category){
        parts.innerHTML = '<option value="">-- select or type --</option>';
        if (!category) return;
        fetch('<?php echo site_url('stock/category_items'); ?>?category=' + encodeURIComponent(category))
            .then(function(res){ return res.json(); })
            .then(function(list){
                list.forEach(function(p){
                    var o = document.createElement('option'); o.value = p; o.text = p; parts.appendChild(o);
                });
            }).catch(function(){ /* ignore */ });
    }
    if (cat) cat.addEventListener('change', function(){ loadParts(this.value); });
    // combine selection/custom on submit
    var form = document.querySelector('form[action="<?php echo site_url('stock/add'); ?>"]');
    if (form) form.addEventListener('submit', function(e){
        var sel = parts.value || '';
        var cust = custom.value || '';
        if (!sel && cust) {
            // copy custom into part_name for backend
            var hidden = document.createElement('input'); hidden.type='hidden'; hidden.name='part_name'; hidden.value = cust; form.appendChild(hidden);
        } else if (sel) {
            var hidden = document.createElement('input'); hidden.type='hidden'; hidden.name='part_name'; hidden.value = sel; form.appendChild(hidden);
        }
    });
    // show add category
    var show = document.getElementById('show-add-category');
    if (show) show.addEventListener('click', function(e){ e.preventDefault(); document.getElementById('add-category-form').style.display='block'; });
    // AJAX submit for add-category form: add new category to select and select it
    var catSubmit = document.getElementById('category-add-submit');
    if (catSubmit) {
        catSubmit.addEventListener('click', function(ev){
            ev.preventDefault();
            var action = '<?php echo site_url('categories/add'); ?>';
            var name = document.getElementById('category-add-name').value || '';
            if (!name) { alert('Enter a category name'); return; }
            var data = new FormData(); data.append('name', name);
            fetch(action, {method:'POST', body: data, headers: {'X-Requested-With':'XMLHttpRequest'}})
                .then(function(r){ return r.json(); })
                .then(function(json){
                    if (json && json.success) {
                            showSuccessModal('Category "' + json.name + '" added', function(){
                                var sel = document.getElementById('part_category');
                                var opt = document.createElement('option');
                                opt.value = json.name;
                                opt.text = json.name;
                                sel.appendChild(opt);
                                sel.value = json.name;
                                document.getElementById('add-category-form').style.display = 'none';
                                document.getElementById('category-add-name').value = '';
                                loadParts(json.name);
                                // after loading parts, focus the part input for quick entry
                                setTimeout(function(){
                                    var partsEl = document.getElementById('part_name');
                                    var customEl = document.getElementById('part_name_custom');
                                    if (partsEl) {
                                        try {
                                            partsEl.focus();
                                            // attempt to open native picker where supported
                                            if (typeof partsEl.showPicker === 'function') {
                                                partsEl.showPicker();
                                            } else {
                                                // fallback: try a synthetic mousedown to hint at opening
                                                try { partsEl.dispatchEvent(new MouseEvent('mousedown')); } catch(e){}
                                            }
                                        } catch(e){}
                                    } else if (customEl) {
                                        try { customEl.focus(); } catch(e){}
                                    }
                                }, 200);
                            });
                        } else {
                            alert('Failed to add category');
                        }
                }).catch(function(){ alert('Failed to add category'); });
        });
    }
    // small modal implementation for confirmation requiring OK
    function showSuccessModal(msg, cb) {
        var modal = document.getElementById('ajax-success-modal');
        var text = document.getElementById('ajax-success-text');
        var ok = document.getElementById('ajax-success-ok');
        if (!modal || !text || !ok) return cb && cb();
        text.innerText = msg;
        modal.style.display = 'block';
        ok.focus();
        function onOk(){ modal.style.display = 'none'; ok.removeEventListener('click', onOk); if (cb) cb(); }
        ok.addEventListener('click', onOk);
    }
});
</script>
<!-- Confirmation modal (requires OK) -->
<div id="ajax-success-modal" style="display:none;position:fixed;left:0;top:0;right:0;bottom:0;z-index:2000;">
    <div style="position:absolute;left:0;top:0;right:0;bottom:0;background:rgba(0,0,0,0.5);"></div>
    <div style="position:relative;max-width:420px;margin:100px auto;background:#fff;padding:20px;border-radius:4px;box-shadow:0 2px 10px rgba(0,0,0,0.3);">
        <p id="ajax-success-text" style="margin:0 0 12px;"></p>
        <div style="text-align:right;"><button id="ajax-success-ok" class="btn btn-primary">OK</button></div>
    </div>
</div>
