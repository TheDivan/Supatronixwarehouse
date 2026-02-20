<?php  defined('BASEPATH') OR exit('No direct script access allowed');

class Export extends FSD_Controller
{
    protected $allowed = ['customers','employees','jobs','job_items','brands','brand_models','offices'];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function index()
    {
        // simple listing for admin to click
        $data['tables'] = $this->allowed;
        $data['page_title'] = 'Exports';
        $data['template'] = 'export/index';
        $this->load->view('master_template', $data);
    }

    public function download($table = '', $format = 'json')
    {
        $table = strtolower($table);
        if (! in_array($table, $this->allowed)) {
            show_error('Invalid export table', 400);
        }

        $query = $this->db->get($table);
        $rows = $query->result_array();

        if ($format === 'json') {
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="$table.json"');
            echo json_encode($rows, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
            return;
        }

        if ($format === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $table . '.csv"');
            $out = fopen('php://output', 'w');
            if (count($rows) > 0) {
                fputcsv($out, array_keys($rows[0]));
                foreach ($rows as $r) {
                    fputcsv($out, $r);
                }
            }
            fclose($out);
            return;
        }

        if ($format === 'xlsx' || $format === 'xls') {
            $this->load->library('excel');
            $spreadsheet = $this->excel->create();
            $sheet = $spreadsheet->getActiveSheet();

            if (count($rows) > 0) {
                $headers = array_keys($rows[0]);
                $sheet->fromArray($headers, NULL, 'A1');
                $row = 2;
                foreach ($rows as $r) {
                    $sheet->fromArray(array_values($r), NULL, 'A' . $row);
                    $row++;
                }
            }

            $filename = $table . '_' . date('Y-m-d_His') . '.' . ($format === 'xlsx' ? 'xlsx' : 'xls');
            $writerType = $format === 'xlsx' ? 'Xlsx' : 'Xls';
            $this->excel->output($filename, $writerType, $spreadsheet);
            return;
        }

        show_error('Unsupported format', 400);
    }
}
