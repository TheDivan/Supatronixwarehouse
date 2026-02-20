<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Suppliers extends FSD_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Supplier_model');
    }

    public function index() {
        $data['suppliers'] = $this->Supplier_model->get_all($this->office_id);
        $this->load->view('master_template', array('content' => $this->load->view('suppliers/index', $data, TRUE)));
    }

    public function add() {
        if (!$this->is_admin) show_error('Permission denied', 403);
        if ($this->input->post()) {
            $post = $this->input->post();
            $this->Supplier_model->insert(array(
                'office_id' => $this->office_id,
                'name' => $post['name'] ?? null,
                'contact' => $post['contact'] ?? null,
                'phone' => $post['phone'] ?? null,
                'email' => $post['email'] ?? null,
                'notes' => $post['notes'] ?? null
            ));
            redirect('suppliers');
        }
        $this->load->view('master_template', array('content' => $this->load->view('suppliers/add', array(), TRUE)));
    }

    public function edit($id = null) {
        if (!$this->is_admin) show_error('Permission denied', 403);
        if (!$id) show_404();
        $s = $this->Supplier_model->get($id);
        if (!$s) show_404();
        if ($this->input->post()) {
            $post = $this->input->post();
            $this->Supplier_model->update($id, array(
                'name' => $post['name'] ?? $s['name'],
                'contact' => $post['contact'] ?? $s['contact'],
                'phone' => $post['phone'] ?? $s['phone'],
                'email' => $post['email'] ?? $s['email'],
                'notes' => $post['notes'] ?? $s['notes']
            ));
            redirect('suppliers');
        }
        $this->load->view('master_template', array('content' => $this->load->view('suppliers/edit', array('supplier'=>$s), TRUE)));
    }

    public function delete($id = null) {
        if (!$this->is_admin) show_error('Permission denied', 403);
        if (!$id) show_404();
        $this->Supplier_model->delete($id);
        redirect('suppliers');
    }

    /**
     * Admin-only: create the suppliers table now if it's missing.
     */
    public function create_table() {
        if (empty($this->is_admin)) show_error('Permission denied', 403);
        $ok = $this->Supplier_model->ensure_table();
        if ($ok) {
            $this->session->set_flashdata('message', 'Suppliers table created or already exists.');
        } else {
            $this->session->set_flashdata('message', 'Failed to create suppliers table. Check logs.');
        }
        redirect('suppliers');
    }
}
