<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier_model extends CI_Model {
    protected $table = 'suppliers';

    public function __construct() {
        parent::__construct();
        if (!$this->db->table_exists($this->table)) {
            $sql = "CREATE TABLE `suppliers` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `office_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `contact` varchar(128) DEFAULT NULL,
  `phone` varchar(64) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
            log_message('error', 'Suppliers table missing; attempting CREATE: ' . $sql);
            $this->db->query($sql);
            $err = $this->db->error();
            if (!empty($err['code'])) {
                log_message('error', 'Failed creating suppliers table: ' . $err['message']);
            } else {
                log_message('info', 'Suppliers table created successfully.');
            }
        }
    }

    public function get_all($office_id = null) {
        try {
            if ($office_id) $this->db->where('office_id', $office_id);
            return $this->db->order_by('name', 'ASC')->get($this->table)->result_array();
        } catch (\Throwable $e) {
            // Table may be missing or another DB error occurred. Attempt to create and return empty list.
            log_message('error', 'Supplier_model::get_all failed: ' . $e->getMessage());
            if (!$this->db->table_exists($this->table)) {
                // Attempt create as constructor would
                $sql = "CREATE TABLE `suppliers` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `office_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `contact` varchar(128) DEFAULT NULL,
  `phone` varchar(64) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
                $this->db->query($sql);
                $err = $this->db->error();
                if (!empty($err['code'])) log_message('error', 'Failed creating suppliers table in recovery: ' . $err['message']);
                else log_message('info', 'Suppliers table created during recovery.');
            }
            return array();
        }
    }

    /**
     * Ensure the suppliers table exists. Returns true on success or if already present.
     */
    public function ensure_table() {
        if ($this->db->table_exists($this->table)) return true;
        $sql = "CREATE TABLE `suppliers` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `office_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `contact` varchar(128) DEFAULT NULL,
  `phone` varchar(64) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $this->db->query($sql);
        $err = $this->db->error();
        if (!empty($err['code'])) {
            log_message('error', 'Supplier_model::ensure_table failed: ' . $err['message']);
            return false;
        }
        log_message('info', 'Supplier_model::ensure_table created suppliers table.');
        return true;
    }

    public function get($id) { return $this->db->where('id', $id)->get($this->table)->row_array(); }

    public function insert($data) {
        $data['created_datetime'] = date('Y-m-d H:i:s');
        $data['updated_datetime'] = $data['created_datetime'];
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        $data['updated_datetime'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function delete($id) { return $this->db->where('id', $id)->delete($this->table); }
}
