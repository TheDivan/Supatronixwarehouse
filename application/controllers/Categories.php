<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends FSD_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Category_model');
    }

    public function index() {
        $data['categories'] = $this->Category_model->get_all();
        $this->load->view('master_template', array('content' => $this->load->view('categories/index', $data, TRUE)));
    }

    public function add() {
        if (empty($this->is_admin)) show_error('Permission denied', 403);
        if ($this->input->post()) {
            $name = trim($this->input->post('name')) ?: null;
            if ($name) {
                $id = $this->Category_model->insert($name);
                $this->session->set_flashdata('message', 'Category added');
                if ($this->input->is_ajax_request()) {
                    // return JSON for AJAX callers
                    $this->output->set_content_type('application/json')->set_output(json_encode(array('success'=>true,'id'=>$id,'name'=>$name)));
                    return;
                }
            }
            redirect('categories');
        }
        $this->load->view('master_template', array('content' => $this->load->view('categories/add', array(), TRUE)));
    }

    public function delete($id = null) {
        if (empty($this->is_admin)) show_error('Permission denied', 403);
        if (!$id) show_404();
        $cat = $this->Category_model->get($id);
        if (!$cat) show_404();
        if (!empty($cat['is_default'])) {
            $this->session->set_flashdata('message', 'Default categories cannot be deleted');
            redirect('categories');
        }
        $this->db->where('id', $id)->delete('stock_categories');
        $this->session->set_flashdata('message', 'Category deleted');
        redirect('categories');
    }

    /**
     * Admin-only: reset categories to canonical list.
     */
    public function reset_defaults() {
        if (empty($this->is_admin)) show_error('Permission denied', 403);
        $canonical = array('Screen/LCD','Touch','Button','Sim Tray','Battery','Speaker','Charging Unit/Block','Software','Back Plate','Camera Glass');
        $ok = $this->Category_model->reset_to_canonical($canonical);
        $this->session->set_flashdata('message', $ok ? 'Categories reset to defaults' : 'Failed resetting categories');
        redirect('categories');
    }
}
