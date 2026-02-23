<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Collections extends FSD_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('collections_model');
    }

    public function index()
    {
        $data = array();
        // Fetch both weekly and monthly datasets for client-side tab switching
        $data['weekly_summary'] = $this->collections_model->get_weekly_profits();
        $data['weekly'] = $this->collections_model->get_weekly();
        $data['monthly_summary'] = $this->collections_model->get_monthly_profits();
        $data['monthly'] = $this->collections_model->get_monthly();

        $data['page_title'] = 'Collections Report';
        $data['template'] = 'collections/index';
        $this->load->view('master_template', $data);
    }

    // JSON endpoints for AJAX if needed
    public function weekly()
    {
        $res = $this->collections_model->get_weekly();
        echo json_encode($res);
    }

    public function monthly()
    {
        $res = $this->collections_model->get_monthly();
        echo json_encode($res);
    }

    // Export CSV for weekly or monthly
    public function export_csv($type = 'weekly')
    {
        if ($type === 'monthly') {
            $rows = $this->collections_model->get_monthly();
            $summary = $this->collections_model->get_monthly_profits();
            $filename = 'collections_monthly_' . date('Y-m-d') . '.csv';
        } else {
            $rows = $this->collections_model->get_weekly();
            $summary = $this->collections_model->get_weekly_profits();
            $filename = 'collections_weekly_' . date('Y-m-d') . '.csv';
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');

        // header row
        fputcsv($out, array('Booking ID','Customer Name','Device Type','Device Model','Fault Description','Date Booked','Date Ready','Date Collected','Amount Charged','Payment Status'));
        foreach ($rows as $r) {
            fputcsv($out, array(
                $r['id'], $r['customer_name'], $r['device_type'], $r['device_model'], $r['fault_description'],
                $r['date_booked'], $r['date_ready'], $r['collected_date'], $r['amount_charged'], $r['payment_status']
            ));
        }
        fclose($out);
    }
}
