<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_tokens extends FSD_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Api_token_model');
    }

    protected function require_admin() {
        if (empty($this->is_admin)) {
            show_error('Permission denied', 403);
        }
    }

    public function index() {
        $this->require_admin();
        $tokens = $this->db->order_by('created_datetime','DESC')->get('api_tokens')->result_array();
        $this->load->view('master_template', array('content' => $this->load->view('api_tokens/index', array('tokens'=>$tokens), TRUE)));
    }

    public function add() {
        $this->require_admin();
        if ($this->input->post()) {
            $post = $this->input->post();
            $token = 'TKN_'.bin2hex(random_bytes(16));
            $data = [
                'employee_id' => isset($post['employee_id']) ? (int)$post['employee_id'] : 0,
                'token' => $token,
                'name' => $post['name'] ?? null,
                'is_admin' => !empty($post['is_admin']) ? 1 : 0,
                'expires' => !empty($post['expires']) ? $post['expires'] : null,
                'created_datetime' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('api_tokens', $data);
            $id = $this->db->insert_id();
            redirect('api_tokens');
        }
        $this->load->view('master_template', array('content' => $this->load->view('api_tokens/add', array(), TRUE)));
    }

    public function edit($id = null) {
        $this->require_admin();
        if (!$id) show_404();
        $t = $this->db->where('id',$id)->get('api_tokens')->row_array();
        if (!$t) show_404();
        if ($this->input->post()) {
            $post = $this->input->post();
            $update = [
                'employee_id' => isset($post['employee_id']) ? (int)$post['employee_id'] : $t['employee_id'],
                'name' => $post['name'] ?? $t['name'],
                'is_admin' => !empty($post['is_admin']) ? 1 : 0,
                'expires' => !empty($post['expires']) ? $post['expires'] : null,
                'created_datetime' => $t['created_datetime']
            ];
            $this->db->where('id',$id)->update('api_tokens',$update);
            redirect('api_tokens');
        }
        $this->load->view('master_template', array('content' => $this->load->view('api_tokens/edit', array('token'=>$t), TRUE)));
    }

    public function delete($id = null) {
        $this->require_admin();
        if (!$id) show_404();
        $this->db->where('id',$id)->delete('api_tokens');
        redirect('api_tokens');
    }

    public function regenerate($id = null) {
        $this->require_admin();
        if (!$id) show_404();
        $t = $this->db->where('id',$id)->get('api_tokens')->row_array();
        if (!$t) show_404();
        $new = 'TKN_'.bin2hex(random_bytes(16));
        $this->db->where('id',$id)->update('api_tokens', array('token'=>$new));
        redirect('api_tokens');
    }
}
