<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_movement_model extends CI_Model {
    protected $table = 'stock_movements';

    public function __construct() {
        parent::__construct();
        // Ensure table exists
        if (!$this->db->table_exists($this->table)) {
            $sql = "CREATE TABLE `stock_movements` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `stock_id` int(10) UNSIGNED NOT NULL,
  `office_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `change` int(10) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
            log_message('error', 'Stock_movements table missing; attempting CREATE: ' . $sql);
            $this->db->query($sql);
            $err = $this->db->error();
            if (!empty($err['code'])) {
                log_message('error', 'Failed creating stock_movements table: ' . $err['message']);
            } else {
                log_message('info', 'Stock_movements table created successfully.');
            }
        }
    }

    public function log($stock_id, $office_id, $change, $note = null, $created_by = null) {
        $data = [
            'stock_id' => $stock_id,
            'office_id' => $office_id,
            'change' => (int)$change,
            'note' => $note,
            'created_by' => $created_by,
            'created_datetime' => date('Y-m-d H:i:s')
        ];
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function get_for_stock($stock_id) {
        return $this->db->where('stock_id', $stock_id)->order_by('created_datetime','DESC')->get($this->table)->result_array();
    }
}
