<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {
    protected $table = 'stock_categories';

    public function __construct() {
        parent::__construct();
        if (!$this->db->table_exists($this->table)) {
            $sql = "CREATE TABLE `stock_categories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `sort_order` int(10) DEFAULT 0,
  `created_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
            log_message('info', 'Creating stock_categories table: ' . $sql);
            $this->db->query($sql);
            $err = $this->db->error();
            if (!empty($err['code'])) log_message('error', 'Failed creating stock_categories: ' . $err['message']);
        }
        // Ensure sort_order column exists for ordering
        if (!$this->db->field_exists('sort_order', $this->table)) {
            $this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `sort_order` int(10) DEFAULT 0");
        }
    }

    public function get_all() {
        return $this->db->order_by('sort_order','ASC')->order_by('name','ASC')->get($this->table)->result_array();
    }

    public function get_by_name($name) {
        return $this->db->where('name', $name)->get($this->table)->row_array();
    }

    /**
     * Reset categories to a canonical ordered list. This will insert missing canonical
     * names, set their sort_order according to the list, and remove any non-canonical entries.
     */
    public function reset_to_canonical(array $canonical) {
        // normalize canonical (trim)
        $canon_norm = array_map(function($v){ return trim($v); }, $canonical);
        $this->db->trans_start();
        // for each canonical name set or insert and update sort_order
        foreach ($canon_norm as $i => $name) {
            $row = $this->db->where('LOWER(name)=', strtolower($name))->get($this->table)->row_array();
            if ($row) {
                $this->db->where('id', $row['id'])->update($this->table, array('name'=>$name,'sort_order'=>$i));
            } else {
                $this->db->insert($this->table, array('name'=>$name,'sort_order'=>$i,'created_datetime'=>date('Y-m-d H:i:s')));
            }
        }
        // delete any rows not in canonical (case-insensitive)
        $inList = array_map(function($v){ return $this->db->escape(strtolower($v)); }, $canon_norm);
        $inClause = implode(',', $inList);
        $this->db->query("DELETE FROM `{$this->table}` WHERE LOWER(name) NOT IN ($inClause)");
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function insert($name) {
        $name = trim((string)$name);
        if ($name === '') return null;
        // Normalize for near-duplicate detection: lowercase, remove non-alphanum
        $norm = preg_replace('/[^a-z0-9]+/', '', strtolower($name));
        // lookup existing rows and compare normalized forms
        $rows = $this->db->select('id,name')->get($this->table)->result_array();
        foreach ($rows as $r) {
            $rnorm = preg_replace('/[^a-z0-9]+/', '', strtolower($r['name']));
            if ($rnorm === $norm) {
                // exact normalized match — return existing id
                return (int)$r['id'];
            }
        }
        // If no exact normalized match, perform Levenshtein distance checks
        // to avoid inserting near-duplicates (typos, punctuation differences)
        $bestId = null;
        $bestDist = PHP_INT_MAX;
        foreach ($rows as $r) {
            $rnorm = preg_replace('/[^a-z0-9]+/', '', strtolower($r['name']));
            if ($rnorm === '') continue;
            $dist = levenshtein($norm, $rnorm);
            if ($dist < $bestDist) { $bestDist = $dist; $bestId = (int)$r['id']; }
        }
        if ($bestId !== null) {
            // compute a threshold: allow up to 2 edits or up to 15% of length
            $maxLen = max(strlen($norm), 1);
            $threshold = max(2, (int)floor($maxLen * 0.15));
            if ($bestDist <= $threshold) {
                return $bestId;
            }
        }
        $now = date('Y-m-d H:i:s');
        $this->db->insert($this->table, array('name'=>$name,'created_datetime'=>$now));
        return $this->db->insert_id();
    }

    public function get($id) {
        return $this->db->where('id',$id)->get($this->table)->row_array();
    }
}
