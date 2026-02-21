<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends FSD_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Stock_model');
        $this->load->model('Stock_movement_model');
        $this->load->model('Api_token_model');
        $this->load->model('Supplier_model');
        $this->load->model('Category_model');
        $this->load->model('Store_location_model');
    }

    // List stock items
    public function index() {
        $category = $this->input->get('category') ?: null;
        // office filter: allow admin to view all, otherwise restrict to session office
        $office_filter = $this->input->get('office_id');
        if ($office_filter === null || $office_filter === '') {
            // if not admin, default to user's office
            if (empty($this->is_admin)) {
                $office_filter = $this->office_id ?: 1;
            } else {
                $office_filter = null; // admin: show all by default
            }
        } else {
            $office_filter = (int)$office_filter;
        }
        // supplier filter
        $supplier_filter = $this->input->get('supplier_id') ?: null;
        $stocks = $this->Stock_model->get_all($office_filter, $category, $supplier_filter);
        // load suppliers to map names -> ids
        $suppliers = $this->Supplier_model->get_all();
        $smap = array();
        foreach ($suppliers as $sp) {
            if (!empty($sp['name'])) $smap[strtolower($sp['name'])] = $sp['id'];
        }
        foreach ($stocks as &$st) {
            $lname = strtolower(trim($st['supplier'] ?? ''));
            $st['supplier_id'] = $smap[$lname] ?? null;
        }
        unset($st);
        $data['stocks'] = $stocks;
        $data['active_category'] = $category;
        $data['active_office'] = $office_filter;
        $data['active_supplier'] = $supplier_filter;
        // use store_locations from store_locations table
        $locations = $this->Store_location_model->get_all();
        $locmap = array();
        foreach ($locations as $loc) $locmap[$loc['id']] = $loc['name'];
        $data['office_names'] = $locmap;
        $data['suppliers'] = $suppliers;
        // Pass any post-save flashdata to the view so it can show a confirmation modal
        $data['saved'] = $this->session->flashdata('saved');
        $this->load->view('master_template', array('content' => $this->load->view('stock/index', $data, TRUE)));
    }

    // Admin: seed default parts into stock for current office
    public function seed_defaults() {
        if (empty($this->is_admin)) show_error('Permission denied', 403);
        $office = $this->office_id ?: 1;
        // Ensure canonical categories exist and then seed stock items per category
        $canonical = array(
            'Screen/LCD','Touch','Button','Sim Tray','Battery','Speaker','Charging Unit/Block','Software','Back Plate','Camera Glass'
        );
        foreach ($canonical as $cname) {
            $exists = $this->Category_model->get_by_name($cname);
            if (!$exists) {
                $this->Category_model->insert($cname);
            }
        }
        $inserted = $this->Stock_model->seed_defaults($office, $canonical);
        $this->session->set_flashdata('seeded', $inserted);
        redirect('stock/seed_report');
    }

    public function seed_report() {
        if (empty($this->is_admin)) show_error('Permission denied', 403);
        $inserted = $this->session->flashdata('seeded') ?: array();
        $data['inserted'] = $inserted;
        $this->load->view('master_template', array('content' => $this->load->view('stock/seed_report', $data, TRUE)));
    }

    // Add new stock item (simple form post)
    public function add() {
        // allow any logged-in user to add stock; office allocation restricted to admins
        $this->authorization();
        if ($this->input->post()) {
            $post = $this->input->post();
            // allow admins to specify office allocation; non-admins use their session office
            $office_id = $this->office_id;
            if (!empty($post['office_id']) && $this->is_admin) {
                $office_id = (int)$post['office_id'];
            }
            $record = array(
                'office_id' => $office_id,
                'part_category' => $post['part_category'] ?? null,
                'part_name' => $post['part_name'] ?? null,
                'device_model' => $post['device_model'] ?? null,
                'quantity' => isset($post['quantity']) ? (int)$post['quantity'] : 0,
                'cost' => isset($post['cost']) ? (float)$post['cost'] : null,
                'supplier' => null,
                'supplier_id' => null,
                'notes' => $post['notes'] ?? null
            );
            // accept supplier_id if provided
            if (!empty($post['supplier_id'])) {
                $record['supplier_id'] = (int)$post['supplier_id'];
                // also set supplier name for backward compatibility
                $sp = $this->Supplier_model->get($record['supplier_id']);
                $record['supplier'] = $sp['name'] ?? null;
            }
            // Server-side validation: ensure required fields present
            if (empty($record['part_category']) || empty($record['part_name']) || empty($record['device_model']) || empty($record['supplier_id'])) {
                $this->session->set_flashdata('message', 'Please provide Category, Part Name, Device Model and Supplier.');
                redirect('stock/add');
            }

            // Insert or merge; Stock_model will set last_was_merge/last_delta when it merges
            $id = $this->Stock_model->insert($record);
            if (!$id) {
                $this->session->set_flashdata('message', 'Failed to add stock item — category may not exist. Contact admin.');
                redirect('stock/add');
            }
            if ($id && !empty($this->Stock_model->last_was_merge)) {
                // log the merged addition as a stock movement
                $delta = (int)$this->Stock_model->last_delta;
                if ($delta !== 0) {
                    $this->Stock_movement_model->log($id, $office_id, $delta, 'Merged stock add', $this->employee_id);
                }
                $this->session->set_flashdata('saved', array('id'=>$id,'part_name'=>$record['part_name'],'merged'=>true,'delta'=>$delta));
                redirect('stock');
            }
            // Log initial stock movement (use the office actually assigned to the record)
            if ($id && isset($record['quantity']) && $record['quantity'] != 0) {
                $this->Stock_movement_model->log($id, $office_id, (int)$record['quantity'], 'Initial stock', $this->employee_id);
            }
            // set flash so index can show a confirmation modal
            $this->session->set_flashdata('saved', array('id'=>$id,'part_name'=>$record['part_name']));
            redirect('stock');
        }
        // pass suppliers, categories and office names
        $suppliers = $this->Supplier_model->get_all();
        $categories = $this->Category_model->get_all();
        $store_locations = $this->Store_location_model->get_all();
        $locmap = array();
        foreach ($store_locations as $l) $locmap[$l['id']] = $l['name'];
        $data = array(
            'suppliers'=>$suppliers,
            'categories'=>$categories,
            'store_locations'=>$store_locations,
            'office_names' => $locmap
        );
        $this->load->view('master_template', array('content' => $this->load->view('stock/add', $data, TRUE)));
    }

    public function edit($id = null) {
        if (empty($this->is_admin)) {
            show_error('Permission denied', 403);
        }
        if (!$id) show_404();
        $item = $this->Stock_model->get($id);
        if (!$item) show_404();
        if ($this->input->post()) {
            $post = $this->input->post();
            $new_qty = isset($post['quantity']) ? (int)$post['quantity'] : (int)$item['quantity'];
            $this->Stock_model->update($id, array(
                'part_category' => $post['part_category'] ?? $item['part_category'],
                'part_name' => $post['part_name'] ?? $item['part_name'],
                'quantity' => $new_qty,
                'cost' => isset($post['cost']) ? (float)$post['cost'] : $item['cost'],
                'supplier' => $post['supplier'] ?? $item['supplier'],
                'notes' => $post['notes'] ?? $item['notes']
            ));
            $delta = $new_qty - (int)$item['quantity'];
            if ($delta != 0) {
                $this->Stock_movement_model->log($id, $this->office_id, $delta, 'Adjusted via edit', $this->employee_id);
            }
            redirect('stock');
        }
        $this->load->view('master_template', array('content' => $this->load->view('stock/edit', array('item'=>$item), TRUE)));
    }

    public function delete($id = null) {
        if (empty($this->is_admin)) {
            show_error('Permission denied', 403);
        }
        if (!$id) show_404();
        $item = $this->Stock_model->get($id);
        if (!$item) show_404();
        // simple delete
        $this->db->where('id', $id)->delete('stock');
        $this->Stock_movement_model->log($id, $this->office_id, -((int)$item['quantity']), 'Deleted item', $this->employee_id);
        redirect('stock');
    }

    public function movements() {
        $id = $this->input->get('stock_id');
        if ($id) {
            $data['movements'] = $this->Stock_movement_model->get_for_stock($id);
            $this->load->view('master_template', array('content' => $this->load->view('stock/movements', $data, TRUE)));
        } else {
            show_404();
        }
    }

    // AJAX: return JSON array of distinct part_name for a given category
    public function category_items() {
        $category = $this->input->get('category');
        header('Content-Type: application/json');
        if (!$category) { echo json_encode(array()); return; }
        $rows = $this->db->select('distinct part_name')->where('part_category', $category)->get('stock')->result_array();
        $out = array();
        foreach ($rows as $r) if (!empty($r['part_name'])) $out[] = $r['part_name'];
        echo json_encode($out);
    }

    // API: adjust stock quantity
    // POST JSON: { "stock_id": 12, "change": -1, "note": "Used for repair" }
    public function api_adjust() {
        // require POST
        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);
        if (!is_array($input)) {
            $input = $this->input->post();
        }
        $stock_id = isset($input['stock_id']) ? (int)$input['stock_id'] : null;
        $change = isset($input['change']) ? (int)$input['change'] : null;
        $note = isset($input['note']) ? $input['note'] : null;

        // Authenticate: either logged-in employee or API token
        $actor_id = $this->employee_id ?? null;
        $actor_is_admin = $this->is_admin ?? false;
        if (!$actor_id) {
            $token = $this->input->get_request_header('X-API-Token', TRUE) ?: ($this->input->post('token') ?: $this->input->get('token'));
            if ($token) {
                $tok = $this->Api_token_model->get_by_token($token);
                if ($tok) {
                    $actor_id = $tok['employee_id'];
                    $actor_is_admin = !empty($tok['is_admin']);
                }
            }
        }
        if (!$actor_id) {
            $this->output->set_status_header(403);
            echo json_encode(['success'=>false,'error'=>'Authentication required']);
            return;
        }

        if (!$stock_id || $change === null) {
            $this->output->set_status_header(400);
            echo json_encode(['success'=>false,'error'=>'Missing stock_id or change']);
            return;
        }

        $item = $this->Stock_model->get($stock_id);
        if (!$item) {
            $this->output->set_status_header(404);
            echo json_encode(['success'=>false,'error'=>'Stock item not found']);
            return;
        }

        // perform quantity adjustment
        $ok = $this->Stock_model->adjust_quantity($stock_id, $change);
        if ($ok === false) {
            $this->output->set_status_header(500);
            echo json_encode(['success'=>false,'error'=>'Failed to adjust quantity']);
            return;
        }

        // log movement
        $this->Stock_movement_model->log($stock_id, $this->office_id, $change, $note, $actor_id);

        // return new quantity
        $new = $this->Stock_model->get($stock_id);
        $this->output->set_content_type('application/json')->set_output(json_encode(['success'=>true,'stock_id'=>$stock_id,'new_quantity'=>(int)$new['quantity']]));
    }
}
