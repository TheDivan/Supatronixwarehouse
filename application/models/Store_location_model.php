<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Store_location_model extends CI_Model {
    protected $table = 'store_locations';

    public function __construct() {
        parent::__construct();
        if (!$this->db->table_exists($this->table)) {
            $sql = "CREATE TABLE `store_locations` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `created_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_store_locations_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($sql);
        }
        // seed default two locations if missing
        $this->seed_defaults();
    }

    public function seed_defaults() {
        $defaults = array('Walvis Bay', 'Swakopmund');
        $now = date('Y-m-d H:i:s');
        foreach ($defaults as $name) {
            $exists = $this->db->where('LOWER(name)=', strtolower($name))->get($this->table)->row_array();
            if (!$exists) {
                $this->db->insert($this->table, array('name'=>$name,'created_datetime'=>$now));
            }
        }
    }

    public function get_all() {
        return $this->db->order_by('id','ASC')->get($this->table)->result_array();
    }

    public function get($id) { return $this->db->where('id',(int)$id)->get($this->table)->row_array(); }

    public function insert($name) {
        $name = trim($name);
        if ($name === '') return null;
        $now = date('Y-m-d H:i:s');
        $this->db->insert($this->table, array('name'=>$name,'created_datetime'=>$now));
        return $this->db->insert_id();
    }
}
