<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_model extends CI_Model {
    protected $table = 'stock';
    // last insert/merge status for controller to inspect
    public $last_was_merge = false;
    public $last_delta = 0;

    public function __construct() {
        parent::__construct();
        // Ensure the stock table exists; if not, create it and seed common parts.
        if (!$this->db->table_exists($this->table)) {
            $sql = "CREATE TABLE `stock` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `office_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `part_category` varchar(64) DEFAULT NULL,
  `part_name` varchar(128) DEFAULT NULL,
  `quantity` int(10) DEFAULT '0',
  `cost` decimal(10,2) DEFAULT NULL,
  `supplier` varchar(128) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
            // Log the CREATE statement for debugging and audit.
            log_message('error', 'Stock table missing; attempting CREATE: ' . $sql);
            $this->db->query($sql);
            $err = $this->db->error();
            if (!empty($err['code'])) {
                log_message('error', 'Failed creating stock table: ' . $err['message']);
            } else {
                log_message('info', 'Stock table created successfully.');
            }
            // Seed with common parts (office_id 1 by default)
            $defaults = array(
                array('part_category'=>'Screen','part_name'=>'Screen - generic','quantity'=>0),
                array('part_category'=>'Speaker','part_name'=>'Speaker - internal','quantity'=>0),
                array('part_category'=>'Charging','part_name'=>'Charging port / unit','quantity'=>0),
                array('part_category'=>'Button','part_name'=>'Home/Power/Volume button','quantity'=>0),
                array('part_category'=>'Motherboard','part_name'=>'Motherboard','quantity'=>0),
                array('part_category'=>'Battery','part_name'=>'Battery','quantity'=>0),
                array('part_category'=>'SIM','part_name'=>'SIM tray / holder','quantity'=>0),
                array('part_category'=>'Touch','part_name'=>'Phone touch / digitizer','quantity'=>0),
            );
            $now = date('Y-m-d H:i:s');
            foreach ($defaults as $d) {
                $ins = array(
                    'office_id' => 1,
                    'part_category' => $d['part_category'],
                    'part_name' => $d['part_name'],
                    'quantity' => $d['quantity'],
                    'created_datetime' => $now,
                    'updated_datetime' => $now
                );
                $this->db->insert($this->table, $ins);
            }
        }
        // Ensure useful columns exist for new features: device_model, supplier_id, created_by
        if ($this->db->table_exists($this->table)) {
            if (!$this->db->field_exists('device_model', $this->table)) {
                @$this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `device_model` varchar(128) DEFAULT NULL");
            }
            if (!$this->db->field_exists('supplier_id', $this->table)) {
                @$this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `supplier_id` int(10) DEFAULT NULL");
            }
            if (!$this->db->field_exists('created_by', $this->table)) {
                @$this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `created_by` int(10) DEFAULT NULL");
            }
            if (!$this->db->field_exists('created_datetime', $this->table)) {
                @$this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `created_datetime` datetime DEFAULT NULL");
            }
            if (!$this->db->field_exists('cost', $this->table)) {
                @$this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `cost` decimal(10,2) DEFAULT NULL");
            }
        }
    }

    /**
     * Get all stock items. If $office_id is provided, restrict to that office.
     * If $category is provided, restrict to that part_category.
     */
    public function get_all($office_id = null, $category = null) {
        if ($office_id) $this->db->where('office_id', $office_id);
        if ($category) $this->db->where('part_category', $category);
        $rows = $this->db->order_by('created_datetime', 'DESC')->get($this->table)->result_array();
        // If requesting for a specific office and no rows exist, seed canonical defaults for that office
        if ($office_id && empty($rows)) {
            // attempt to use Category_model canonical list if available
            if (class_exists('Category_model')) {
                $CI =& get_instance();
                $CI->load->model('Category_model');
                $cats = array_map(function($r){ return $r['name']; }, $CI->Category_model->get_all());
                $this->seed_defaults($office_id, $cats);
            } else {
                $this->seed_defaults($office_id, null);
            }
            // re-query after seeding
            $this->db->where('office_id', $office_id);
            if ($category) $this->db->where('part_category', $category);
            $rows = $this->db->order_by('created_datetime', 'DESC')->get($this->table)->result_array();
        }
        return $rows;
    }

    public function get($id) {
        return $this->db->where('id', $id)->get($this->table)->row_array();
    }

    public function insert($data) {
        // Normalize inputs
        $office_id = isset($data['office_id']) ? (int)$data['office_id'] : null;
        $category = isset($data['part_category']) ? $data['part_category'] : null;
        $part_name = isset($data['part_name']) ? $data['part_name'] : null;
        $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 0;

        // Ensure category is present and exists in categories table
        if (!$category) return null;
        $cat = $this->db->where('name', $category)->get('stock_categories')->row_array();
        if (!$cat) {
            // do not auto-create non-default categories here; require admin to add
            return null;
        }

        // Reset merge flags
        $this->last_was_merge = false;
        $this->last_delta = 0;

        // If a duplicate exists (same part_name + category + device_model + office_id [+ supplier_id if provided]), merge quantities
        $dupQuery = $this->db->where('office_id', $office_id)
                              ->where('part_category', $category)
                              ->where('part_name', $part_name);
        if (!empty($data['device_model'])) $dupQuery = $dupQuery->where('device_model', $data['device_model']);
        if (!empty($data['supplier_id'])) $dupQuery = $dupQuery->where('supplier_id', $data['supplier_id']);
        $existing = $dupQuery->get($this->table)->row_array();
        if ($existing) {
            // perform merge: increment quantity and update optional fields
            $delta = (int)$quantity;
            if ($delta !== 0) {
                $this->db->set('quantity', 'quantity + ' . $delta, FALSE);
            }
            $upd = array();
            if (isset($data['cost'])) $upd['cost'] = $data['cost'];
            if (isset($data['supplier'])) $upd['supplier'] = $data['supplier'];
            if (isset($data['supplier_id']) && $this->db->field_exists('supplier_id', $this->table)) $upd['supplier_id'] = $data['supplier_id'];
            if (isset($data['notes'])) $upd['notes'] = $data['notes'];
            if (isset($data['device_model'])) $upd['device_model'] = $data['device_model'];
            if (!empty($upd)) $this->db->where('id', $existing['id'])->update($this->table, $upd);
            $this->last_was_merge = true;
            $this->last_delta = $delta;
            return (int)$existing['id'];
        }

        $data['created_datetime'] = date('Y-m-d H:i:s');
        $data['updated_datetime'] = $data['created_datetime'];
        // If the DB schema doesn't have supplier_id yet, remove it to avoid SQL errors
        if (isset($data['supplier_id']) && !$this->db->field_exists('supplier_id', $this->table)) {
            unset($data['supplier_id']);
        }
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        $data['updated_datetime'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function adjust_quantity($id, $delta) {
        return $this->db->set('quantity', 'quantity + ' . (int)$delta, FALSE)->where('id', $id)->update($this->table);
    }

    /**
     * Seed default stock parts for an office if they don't already exist.
     * Returns number inserted.
     */
    public function seed_defaults($office_id = 1, $categories = null) {
        // If categories not provided, use the canonical defaults
        if ($categories === null) {
            $categories = array(
                'Screen/LCD',
                'Touch',
                'Button',
                'Sim Tray',
                'Battery',
                'Speaker',
                'Charging Unit/Block',
                'Software',
                'Back Plate',
                'Camera Glass'
            );
        }
        // Build default stock items: one item per category (part_name same as category)
        $defaults = array();
        foreach ($categories as $c) {
            $defaults[] = array('part_category' => $c, 'part_name' => $c, 'quantity' => 0);
        }
        $now = date('Y-m-d H:i:s');
        $inserted = array();
        foreach ($defaults as $d) {
            $exists = $this->db->where('office_id', $office_id)->where('part_name', $d['part_name'])->count_all_results($this->table);
            if ($exists) continue;
            $ins = array(
                'office_id' => $office_id,
                'part_category' => $d['part_category'],
                'part_name' => $d['part_name'],
                'quantity' => $d['quantity'],
                'created_datetime' => $now,
                'updated_datetime' => $now
            );
            $this->db->insert($this->table, $ins);
            $inserted[] = array('id'=>$this->db->insert_id(),'part_name'=>$d['part_name'],'office_id'=>$office_id);
        }
        return $inserted;
    }
}
