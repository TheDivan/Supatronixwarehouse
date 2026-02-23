<?php  defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends FSD_Controller
{
	var $before_filter = array('name' => 'authorization', 'except' => array('login', 'forgot_password'));
	var $offices = array();
    
	public function __construct()
	{
		parent::__construct();
		$this->load->model('customer_model');
        $this->load->model('geo_model');
        $this->offices = $this->geo_model->get_all_offices();

        ## Only Admin Allow funcations ##
        if (!$this->session->is_admin && $this->router->method == 'ajax_delete') 
        {
            show_error('You are not authorized to access this page.', 403);
        }
	}

	public function index()
	{
        $data['offices'] = $this->offices;
		$data['page_title'] = 'Customers';
		$data['template'] = "customer/listing";
		$this->load->view("master_template", $data);
	}
	
	public function ajax_list()
    {
        $list = $this->customer_model->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $customer) {
            $no++;
            $row = array();
            $row[] = $customer->name;
            $row[] = $customer->phone;
            $row[] = $customer->email;
            $row[] = $customer->updated_datetime;
            $row[] = $customer->created_datetime;
 
            //add html for action
            $buttons = '<button class="btn btn-xs btn-primary" onclick="edit('."'".$customer->id."'".')"><i class="fa fa-pencil"></i> Edit</button>';
            $buttons .= ' <a class="btn btn-xs btn-info" href="'.site_url('customers/history/'.$customer->id).'" title="History"><i class="fa fa-history"></i> History</a>';
            if($this->session->is_admin)
                $buttons .= '<button class="btn btn-xs btn-danger" onclick="del('."'".$customer->id."'".')"><i class="fa fa-trash"></i> Delete</button>';

            $row[] = $buttons;
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->customer_model->count_all(),
                        "recordsFiltered" => $this->customer_model->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
 
    public function ajax_edit($id)
    {
        $data = $this->customer_model->get_by_id($id);
        //$data->dob = ($data->dob == '0000-00-00') ? '' : $data->dob; // if 0000-00-00 set tu empty for datepicker compatibility
        echo json_encode($data);
    }
 
    public function ajax_add()
    {
        $this->_validate();
        $data = $this->input->post(NULL, TRUE);
        $data['office_id'] = $this->session->office_id;
		$data['updated_datetime'] = date("Y-m-d H:i:s");
		$data['created_datetime'] = date("Y-m-d H:i:s");
        $insert = $this->customer_model->save($data);
        echo json_encode(array("status" => TRUE, 'id' => $insert));
    }

    /**
     * Show customer's job history
     */
    public function history($customer_id)
    {
        if (!is_numeric($customer_id)) show_404();
        $this->load->model('job_model');
        $jobs = $this->job_model->get_where(array('customer_id' => $customer_id));
        // attach first device/fault for summary
        foreach ($jobs as &$j) {
            $items = $this->job_model->get_items($j['id']);
            $j['first_device'] = isset($items[0]) ? $items[0]['device_number'] : '';
            $j['first_fault'] = isset($items[0]) ? $items[0]['fault_discription'] : '';
        }
        $data = array('jobs' => $jobs, 'customer' => $this->customer_model->get_by_id($customer_id));
        $this->load->view('master_template', array('content' => $this->load->view('customer/history', $data, TRUE)));
    }
 
    public function ajax_update()
    {
        $this->_validate();
        $data = $this->input->post(NULL, TRUE);
		$data['updated_datetime'] = date("Y-m-d H:i:s");
        $this->customer_model->update(array('id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    }
 
    public function ajax_delete($id)
    {
        $this->customer_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
 
 
    private function _validate()
    {
        $data = array();
        $post = $this->input->post(NULL, TRUE);
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;
 
        if($post['name'] == '')
        {
            $data['inputerror'][] = 'name';
            $data['error_string'][] = 'Name is required';
            $data['status'] = FALSE;
        }
        elseif (strlen($post['name']) > 255) 
        {
            $data['inputerror'][] = 'name';
            $data['error_string'][] = 'Name can not be grather then 255 characters.';
            $data['status'] = FALSE;
        }        
 
        if($post['phone'] == '')
        {
            $data['inputerror'][] = 'phone';
            $data['error_string'][] = 'Phone is required';
            $data['status'] = FALSE;
        }
        elseif (strlen($post['phone']) > 32) 
        {
            $data['inputerror'][] = 'phone';
            $data['error_string'][] = 'Phone can not be grather then 32 characters.';
            $data['status'] = FALSE;
        }         
 
        if($post['email'] == '')
        {
            $data['inputerror'][] = 'email';
            $data['error_string'][] = 'Email is required';
            $data['status'] = FALSE;
        }
        elseif(filter_var($post['email'], FILTER_VALIDATE_EMAIL) === FALSE)
        {
            $data['inputerror'][] = 'email';
            $data['error_string'][] = 'Invalid email address.';
            $data['status'] = FALSE;	
        }
        elseif (strlen($post['email']) > 64) 
        {
            $data['inputerror'][] = 'email';
            $data['error_string'][] = 'Email can not be grather then 64 characters.';
            $data['status'] = FALSE;
        }
        else 
        {
            if(isset($post['id']) && !empty($post['id']))
        	   $res = $this->customer_model->get_where(array('email' => $post['email'], 'id <>' => $this->input->post('id')));
            else
                $res = $this->customer_model->get_where(array('email' => $post['email']));
        	if(is_array($res) && count($res) > 0)
        	{
	            $data['inputerror'][] = 'email';
	            $data['error_string'][] = 'Email address already exist`.';
	            $data['status'] = FALSE;        		
        	}
        }       

 
        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }
    }

    /**
     * Export all customers to Excel
     * Supports formats: xlsx, xls, csv
     */
    public function export_all()
    {
        $format = $this->input->get('format', TRUE) ?: 'xlsx';
        $list = $this->customer_model->get_all_for_export();
        
        $this->load->library('excel');
        $spreadsheet = $this->excel->create();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = array('ID', 'Name', 'Phone', 'Email', 'Office', 'Created', 'Updated');
        $sheet->fromArray($headers, NULL, 'A1');
        
        // Add data rows
        $row = 2;
        foreach ($list as $customer) {
            $sheet->setCellValue('A'.$row, $customer->id);
            $sheet->setCellValue('B'.$row, $customer->name);
            $sheet->setCellValue('C'.$row, $customer->phone);
            $sheet->setCellValue('D'.$row, $customer->email);
            $sheet->setCellValue('E'.$row, $customer->office_id);
            $sheet->setCellValue('F'.$row, $customer->created_datetime);
            $sheet->setCellValue('G'.$row, $customer->updated_datetime);
            $row++;
        }
        
        // Set column widths
        $columns = array('A' => 10, 'B' => 25, 'C' => 20, 'D' => 30, 'E' => 15, 'F' => 20, 'G' => 20);
        foreach ($columns as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
        
        $filename = 'customers_'.date('Y-m-d_His').'.'.$format;
        $this->excel->output($filename, ucfirst($format), $spreadsheet);
    }

    /**
     * Export single customer record to Excel
     */
    public function export_single($id)
    {
        if (!is_numeric($id)) {
            show_error('Invalid customer ID', 400);
        }
        
        $customer = $this->customer_model->get_by_id($id);
        if (empty($customer)) {
            show_error('Customer not found', 404);
        }
        
        $format = $this->input->get('format', TRUE) ?: 'xlsx';
        
        $this->load->library('excel');
        $spreadsheet = $this->excel->create();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', 'Customer Details');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        // Customer information
        $row = 3;
        $labels = array('ID', 'Name', 'Phone', 'Email', 'Office ID', 'Created', 'Updated', 'Notes');
        $values = array($customer->id, $customer->name, $customer->phone, $customer->email, 
                       $customer->office_id, $customer->created_datetime, $customer->updated_datetime, 
                       $customer->notes ?? '');
        
        foreach ($labels as $key => $label) {
            $sheet->setCellValue('A'.$row, $label.':');
            $sheet->getStyle('A'.$row)->getFont()->setBold(true);
            $sheet->setCellValue('B'.$row, $values[$key] ?? '');
            $row++;
        }
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        
        $filename = 'customer_'.$id.'_'.date('Y-m-d_His').'.'.$format;
        $this->excel->output($filename, ucfirst($format), $spreadsheet);
    }
}