<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class FSD_Controller extends CI_Controller
{		
	 public function __construct()
	 {
	 	parent::__construct();

		// Note: temporary debug handlers removed. Permanent logging is via CI log_message().
	 	## Ajax funcations can be called by ajax request ##
	 	if (substr($this->router->method, 0, 4) == 'ajax' && !$this->input->is_ajax_request()) 
        {
            show_404();
        }

		// Initialize commonly-used properties from session so controllers can access them
		// (e.g. $this->office_id, $this->employee_id, $this->is_admin)
		$this->employee_id = $this->session->userdata('employee_id') ?? null;
		$this->office_id = $this->session->userdata('office_id') ?? null;
		$this->is_admin = $this->session->userdata('is_admin') ?? false;
		$this->name = $this->session->userdata('name') ?? null;
	 }
	 
	 public function authorization() 
	 {
	 	if( $this->session->userdata('is_logged_in') !==  TRUE )
	   	{
	 		redirect('login?return_url='.str_replace($this->config->item('url_suffix'), "", current_url()));	
	   	}
	 }

	/**
	 * _remap wrapper to provide structured JSON error responses for AJAX requests
	 * and centralized exception logging. This prevents raw PHP warnings/errors
	 * from polluting AJAX JSON responses.
	 */
	public function _remap($method, $params = array())
	{
		try {
			if (method_exists($this, $method)) {
				return call_user_func_array(array($this, $method), $params);
			}
			return show_404();
		} catch (Throwable $e) {
			$msg = sprintf("[%s] Uncaught %s: %s in %s:%d\nStack trace:\n%s\n", date('Y-m-d H:i:s'), get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
			@file_put_contents(APPPATH.'logs/ajax_errors.log', $msg, FILE_APPEND);
			log_message('error', $msg);
			if (isset($this->input) && $this->input->is_ajax_request()) {
				$this->output->set_status_header(500);
				$this->output->set_header('Content-Type: application/json');
				echo json_encode(array('status' => FALSE, 'error' => 'Server error')); // generic message for clients
			} else {
				show_error('A server error occurred. Check application/logs/ajax_errors.log for details.', 500);
			}
			return;
		} catch (Exception $e) {
			// For PHP versions without Throwable
			$msg = sprintf("[%s] Uncaught %s: %s in %s:%d\nStack trace:\n%s\n", date('Y-m-d H:i:s'), get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
			@file_put_contents(APPPATH.'logs/ajax_errors.log', $msg, FILE_APPEND);
			log_message('error', $msg);
			if (isset($this->input) && $this->input->is_ajax_request()) {
				$this->output->set_status_header(500);
				$this->output->set_header('Content-Type: application/json');
				echo json_encode(array('status' => FALSE, 'error' => 'Server error'));
			} else {
				show_error('A server error occurred. Check application/logs/ajax_errors.log for details.', 500);
			}
			return;
		}
	}
}