<?php  defined('BASEPATH') OR exit('No direct script access allowed');

class Email extends FSD_Controller
{
	var $before_filter = array('name' => 'authorization', 'except' => array());
	var $offices = array();
    
	public function __construct()
	{
		parent::__construct();
        $this->load->model('geo_model');
        $this->load->model('email_model');
        $this->offices = $this->geo_model->get_all_offices();		
	}

	public function index()
	{
		$data['offices'] = $this->offices;
		$data['page_title'] = 'Email Templates';
		$data['template'] = "email/listing";
		$this->load->view("master_template", $data);
	}
 
    public function ajax_list()
    {
        $list = $this->email_model->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $email_template) {
            $no++;
            $row = array();
            $row[] = $email_template->title;
            $row[] = $email_template->from_name;
            $row[] = $email_template->from_email;
            $row[] = $email_template->to_email;
            $row[] = $email_template->subject;
 
            //add html for action
            $row[] = '<button class="btn btn-xs btn-primary" onclick="edit('."'".$email_template->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</button>';
         
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->email_model->count_all(),
                        "recordsFiltered" => $this->email_model->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
 
    public function ajax_edit($id)
    {
        $data = $this->email_model->get_by_id($id);
        //$data->dob = ($data->dob == '0000-00-00') ? '' : $data->dob; // if 0000-00-00 set tu empty for datepicker compatibility
        echo json_encode($data);
    }
 
    public function ajax_update()
    {
        $this->_validate();
        $data = array(
            'title' => $this->input->post('title'),
            'from_name' => $this->input->post('from_name'),
            'from_email' => $this->input->post('from_email'),
            'to_email' => $this->input->post('to_email'),
            'subject' => $this->input->post('subject'),
            'message' => $this->input->post('message'),
            'status' => $this->input->post('status')
        );
        $this->email_model->update(array('id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    } 
 
    private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;
 
        if($this->input->post('title') == '')
        {
            $data['inputerror'][] = 'title';
            $data['error_string'][] = 'Title is required';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('from_name') == '')
        {
            $data['inputerror'][] = 'from_name';
            $data['error_string'][] = 'From name is required';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('from_email') == '')
        {
            $data['inputerror'][] = 'from_email';
            $data['error_string'][] = 'From email is required';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('to_email') == '')
        {
            $data['inputerror'][] = 'to_email';
            $data['error_string'][] = 'To email is required';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('subject') == '')
        {
            $data['inputerror'][] = 'subject';
            $data['error_string'][] = 'Subject is required';
            $data['status'] = FALSE;
        }
        
        if($this->input->post('message') == '')
        {
            $data['inputerror'][] = 'message';
            $data['error_string'][] = 'Message is required';
            $data['status'] = FALSE;
        }
        
        if($this->input->post('status') == '')
        {
            $data['inputerror'][] = 'status';
            $data['error_string'][] = 'Status is required';
            $data['status'] = FALSE;
        }        
        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }
    }

    /**
     * Export all email templates to Excel
     * Supports formats: xlsx, xls, csv
     */
    public function export_all()
    {
        $format = $this->input->get('format', TRUE) ?: 'xlsx';
        $list = $this->email_model->get_all_for_export();
        
        $this->load->library('excel');
        $spreadsheet = $this->excel->create();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = array('ID', 'Title', 'From Name', 'From Email', 'To Email', 'Subject', 'Status');
        $sheet->fromArray($headers, NULL, 'A1');
        
        // Add data rows
        $row = 2;
        foreach ($list as $template) {
            $sheet->setCellValue('A'.$row, $template->id);
            $sheet->setCellValue('B'.$row, $template->title);
            $sheet->setCellValue('C'.$row, $template->from_name);
            $sheet->setCellValue('D'.$row, $template->from_email);
            $sheet->setCellValue('E'.$row, $template->to_email);
            $sheet->setCellValue('F'.$row, $template->subject);
            $sheet->setCellValue('G'.$row, $template->status == 1 ? 'Active' : 'Inactive');
            $row++;
        }
        
        // Set column widths
        $columns = array('A' => 10, 'B' => 20, 'C' => 20, 'D' => 25, 'E' => 25, 'F' => 30, 'G' => 15);
        foreach ($columns as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
        
        $filename = 'email_templates_'.date('Y-m-d_His').'.'.$format;
        $this->excel->output($filename, ucfirst($format), $spreadsheet);
    }

    /**
     * Export single email template to Excel
     */
    public function export_single($id)
    {
        if (!is_numeric($id)) {
            show_error('Invalid template ID', 400);
        }
        
        $template = $this->email_model->get_by_id($id);
        if (empty($template)) {
            show_error('Email template not found', 404);
        }
        
        $format = $this->input->get('format', TRUE) ?: 'xlsx';
        
        $this->load->library('excel');
        $spreadsheet = $this->excel->create();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', 'Email Template');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        // Template information
        $row = 3;
        $sheet->setCellValue('A'.$row, 'ID:');
        $sheet->setCellValue('B'.$row, $template->id);
        $row++;
        
        $sheet->setCellValue('A'.$row, 'Title:');
        $sheet->setCellValue('B'.$row, $template->title);
        $row++;
        
        $sheet->setCellValue('A'.$row, 'From Name:');
        $sheet->setCellValue('B'.$row, $template->from_name);
        $row++;
        
        $sheet->setCellValue('A'.$row, 'From Email:');
        $sheet->setCellValue('B'.$row, $template->from_email);
        $row++;
        
        $sheet->setCellValue('A'.$row, 'To Email:');
        $sheet->setCellValue('B'.$row, $template->to_email);
        $row++;
        
        $sheet->setCellValue('A'.$row, 'Subject:');
        $sheet->setCellValue('B'.$row, $template->subject);
        $row++;
        
        $sheet->setCellValue('A'.$row, 'Status:');
        $sheet->setCellValue('B'.$row, $template->status == 1 ? 'Active' : 'Inactive');
        $row += 2;
        
        $sheet->setCellValue('A'.$row, 'Message:');
        $sheet->getStyle('A'.$row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A'.$row, $template->message);
        $sheet->getStyle('A'.$row.':B'.$row)->getAlignment()->setWrapText(true);
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(60);
        
        $filename = 'email_template_'.$id.'_'.date('Y-m-d_His').'.'.$format;
        $this->excel->output($filename, ucfirst($format), $spreadsheet);
    }
}