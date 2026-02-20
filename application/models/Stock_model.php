<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_model extends CI_Model {
    protected $table = 'stock';

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
    }

    /**
     * Get all stock items. If $office_id is provided, restrict to that office.
     * If $category is provided, restrict to that part_category.
     */
    public function get_all($office_id = null, $category = null) {
        if ($office_id) $this->db->where('office_id', $office_id);
        if ($category) $this->db->where('part_category', $category);
        return $this->db->order_by('created_datetime', 'DESC')->get($this->table)->result_array();
    }

    public function get($id) {
        return $this->db->where('id', $id)->get($this->table)->row_array();
    }

    public function insert($data) {
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
