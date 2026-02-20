<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_token_model extends CI_Model {
    protected $table = 'api_tokens';

    public function __construct() { parent::__construct(); }

    public function get_by_token($token) {
        if (!$token) return null;
        return $this->db->where('token', $token)->get($this->table)->row_array();
    }
}
