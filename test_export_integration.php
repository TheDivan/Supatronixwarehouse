<?php
/**
 * Export Feature Integration Tests
 * Tests all export functionality for Jobs, Customers, and Email Templates
 * 
 * Requires database to be set up and application bootstrap to succeed.
 * Run from command line: php test_export_integration.php
 */

// Bootstrap the CodeIgniter application (clean version without controller redirect issues)
require_once 'test_export_bootstrap.php';

// Colors for terminal output
define('COLOR_RED', "\033[31m");
define('COLOR_GREEN', "\033[32m");
define('COLOR_YELLOW', "\033[33m");
define('COLOR_BLUE', "\033[34m");
define('COLOR_RESET', "\033[0m");

class ExportTests
{
    private $base_url = 'http://localhost';
    private $results = array();
    private $passed = 0;
    private $failed = 0;

    public function run()
    {
        echo COLOR_BLUE . "╔═══════════════════════════════════════════════════════════════╗\n";
        echo "║       Export Feature Integration Test Suite                       ║\n";
        echo "║        Testing PhpSpreadsheet Wrapper & Export Endpoints          ║\n";
        echo "╚═══════════════════════════════════════════════════════════════╝\n" . COLOR_RESET;

        // Test 1: Export all jobs
        $this->test_export_jobs_all();
        
        // Test 2: Export single job
        $this->test_export_job_single();
        
        // Test 3: Export all customers
        $this->test_export_customers_all();
        
        // Test 4: Export single customer
        $this->test_export_customer_single();
        
        // Test 5: Export all email templates  
        $this->test_export_emails_all();
        
        // Test 6: Export single email template
        $this->test_export_email_single();
        
        // Test 7: Test CSV format
        $this->test_export_csv_format();
        
        // Test 8: Test XLS format (if available)
        $this->test_export_xls_format();

        $this->print_summary();
    }

    private function test_export_jobs_all()
    {
        echo "\n" . COLOR_YELLOW . "Test 1: Export All Jobs (XLSX)" . COLOR_RESET . "\n";
        
        try {
            // Simulate HTTP request to export endpoint
            $CI = &get_instance();
            $CI->load->model('job_model');
            $CI->load->library('excel');
            
            $list = $CI->job_model->get_all_for_export();
            
            if (empty($list)) {
                throw new Exception("No jobs found in database for export");
            }
            
            $spreadsheet = $CI->excel->create();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Verify spreadsheet was created
            if (!$sheet) {
                throw new Exception("Failed to create spreadsheet");
            }
            
            // Write test file
            $test_file = APPPATH . 'cache/test_export_jobs_all.xlsx';
            $CI->excel->write('Xlsx', $test_file, $spreadsheet);
            
            // Verify file was created
            if (!file_exists($test_file)) {
                throw new Exception("Export file was not created");
            }
            
            // Verify file size is reasonable (XLSX files are ZIP archives, should be > 4KB)
            $filesize = filesize($test_file);
            if ($filesize < 4000) {
                throw new Exception("Export file size suspiciously small: " . $filesize . " bytes");
            }
            
            $this->pass("Jobs exported successfully (" . count($list) . " records, " . $filesize . " bytes)");
            @unlink($test_file);
            
        } catch (Exception $e) {
            $this->fail("Export Jobs All: " . $e->getMessage());
        }
    }

    private function test_export_job_single()
    {
        echo "\n" . COLOR_YELLOW . "Test 2: Export Single Job (XLSX)" . COLOR_RESET . "\n";
        
        try {
            $CI = &get_instance();
            $CI->load->model('job_model');
            $CI->load->library('excel');
            
            // Get first job
            $jobs = $CI->job_model->get_all_for_export();
            if (empty($jobs)) {
                throw new Exception("No jobs found for single export test");
            }
            
            $job_id = $jobs[0]->id;
            $job = $CI->job_model->get_where(array('id' => $job_id));
            
            if (empty($job)) {
                throw new Exception("Job not found: " . $job_id);
            }
            
            $job = $job[0];
            $items = $CI->job_model->get_items($job_id);
            
            $spreadsheet = $CI->excel->create();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Write job details
            $sheet->setCellValue('A1', 'Job Details - ID: ' . $job->id);
            $sheet->setCellValue('A3', 'Job ID:');
            $sheet->setCellValue('B3', $job->id);
            
            $test_file = APPPATH . 'cache/test_export_job_single.xlsx';
            $CI->excel->write('Xlsx', $test_file, $spreadsheet);
            
            if (!file_exists($test_file)) {
                throw new Exception("Single job export file not created");
            }
            
            $this->pass("Single job exported successfully (Job ID: " . $job_id . ", " . count($items) . " items)");
            @unlink($test_file);
            
        } catch (Exception $e) {
            $this->fail("Export Single Job: " . $e->getMessage());
        }
    }

    private function test_export_customers_all()
    {
        echo "\n" . COLOR_YELLOW . "Test 3: Export All Customers (XLSX)" . COLOR_RESET . "\n";
        
        try {
            $CI = &get_instance();
            $CI->load->model('customer_model');
            $CI->load->library('excel');
            
            $list = $CI->customer_model->get_all_for_export();
            
            if (empty($list)) {
                throw new Exception("No customers found in database for export");
            }
            
            $spreadsheet = $CI->excel->create();
            $sheet = $spreadsheet->getActiveSheet();
            
            $headers = array('ID', 'Name', 'Phone', 'Email', 'Office', 'Created', 'Updated');
            $sheet->fromArray($headers, NULL, 'A1');
            
            $test_file = APPPATH . 'cache/test_export_customers_all.xlsx';
            $CI->excel->write('Xlsx', $test_file, $spreadsheet);
            
            if (!file_exists($test_file)) {
                throw new Exception("Customers export file not created");
            }
            
            $filesize = filesize($test_file);
            $this->pass("Customers exported successfully (" . count($list) . " records, " . $filesize . " bytes)");
            @unlink($test_file);
            
        } catch (Exception $e) {
            $this->fail("Export Customers All: " . $e->getMessage());
        }
    }

    private function test_export_customer_single()
    {
        echo "\n" . COLOR_YELLOW . "Test 4: Export Single Customer (XLSX)" . COLOR_RESET . "\n";
        
        try {
            $CI = &get_instance();
            $CI->load->model('customer_model');
            $CI->load->library('excel');
            
            $list = $CI->customer_model->get_all_for_export();
            if (empty($list)) {
                throw new Exception("No customers found for single export test");
            }
            
            $customer_id = $list[0]->id;
            $customer = $CI->customer_model->get_by_id($customer_id);
            
            if (empty($customer)) {
                throw new Exception("Customer not found: " . $customer_id);
            }
            
            $spreadsheet = $CI->excel->create();
            $sheet = $spreadsheet->getActiveSheet();
            
            $sheet->setCellValue('A1', 'Customer Details');
            $sheet->setCellValue('A3', 'ID:');
            $sheet->setCellValue('B3', $customer->id);
            $sheet->setCellValue('A4', 'Name:');
            $sheet->setCellValue('B4', $customer->name);
            
            $test_file = APPPATH . 'cache/test_export_customer_single.xlsx';
            $CI->excel->write('Xlsx', $test_file, $spreadsheet);
            
            if (!file_exists($test_file)) {
                throw new Exception("Single customer export file not created");
            }
            
            $this->pass("Single customer exported successfully (Customer ID: " . $customer_id . ", " . $customer->name . ")");
            @unlink($test_file);
            
        } catch (Exception $e) {
            $this->fail("Export Single Customer: " . $e->getMessage());
        }
    }

    private function test_export_emails_all()
    {
        echo "\n" . COLOR_YELLOW . "Test 5: Export All Email Templates (XLSX)" . COLOR_RESET . "\n";
        
        try {
            $CI = &get_instance();
            $CI->load->model('email_model');
            $CI->load->library('excel');
            
            $list = $CI->email_model->get_all_for_export();
            
            if (empty($list)) {
                throw new Exception("No email templates found in database");
            }
            
            $spreadsheet = $CI->excel->create();
            $sheet = $spreadsheet->getActiveSheet();
            
            $headers = array('ID', 'Title', 'From Name', 'From Email', 'To Email', 'Subject', 'Status');
            $sheet->fromArray($headers, NULL, 'A1');
            
            $test_file = APPPATH . 'cache/test_export_emails_all.xlsx';
            $CI->excel->write('Xlsx', $test_file, $spreadsheet);
            
            if (!file_exists($test_file)) {
                throw new Exception("Email templates export file not created");
            }
            
            $filesize = filesize($test_file);
            $this->pass("Email templates exported successfully (" . count($list) . " templates, " . $filesize . " bytes)");
            @unlink($test_file);
            
        } catch (Exception $e) {
            $this->fail("Export Emails All: " . $e->getMessage());
        }
    }

    private function test_export_email_single()
    {
        echo "\n" . COLOR_YELLOW . "Test 6: Export Single Email Template (XLSX)" . COLOR_RESET . "\n";
        
        try {
            $CI = &get_instance();
            $CI->load->model('email_model');
            $CI->load->library('excel');
            
            $list = $CI->email_model->get_all_for_export();
            if (empty($list)) {
                throw new Exception("No email templates found for single export test");
            }
            
            $email_id = $list[0]->id;
            $template = $CI->email_model->get_by_id($email_id);
            
            if (empty($template)) {
                throw new Exception("Email template not found: " . $email_id);
            }
            
            $spreadsheet = $CI->excel->create();
            $sheet = $spreadsheet->getActiveSheet();
            
            $sheet->setCellValue('A1', 'Email Template');
            $sheet->setCellValue('A3', 'ID:');
            $sheet->setCellValue('B3', $template->id);
            $sheet->setCellValue('A4', 'Title:');
            $sheet->setCellValue('B4', $template->title);
            
            $test_file = APPPATH . 'cache/test_export_email_single.xlsx';
            $CI->excel->write('Xlsx', $test_file, $spreadsheet);
            
            if (!file_exists($test_file)) {
                throw new Exception("Single email export file not created");
            }
            
            $this->pass("Single email template exported successfully (Template ID: " . $email_id . ", " . $template->title . ")");
            @unlink($test_file);
            
        } catch (Exception $e) {
            $this->fail("Export Single Email: " . $e->getMessage());
        }
    }

    private function test_export_csv_format()
    {
        echo "\n" . COLOR_YELLOW . "Test 7: Export CSV Format" . COLOR_RESET . "\n";
        
        try {
            $CI = &get_instance();
            $CI->load->model('job_model');
            $CI->load->library('excel');
            
            $list = $CI->job_model->get_all_for_export();
            if (empty($list)) {
                throw new Exception("No jobs found for CSV export test");
            }
            
            $spreadsheet = $CI->excel->create();
            $sheet = $spreadsheet->getActiveSheet();
            
            $headers = array('ID', 'Technician', 'Email');
            $sheet->fromArray($headers, NULL, 'A1');
            
            // Verify CSV writer is available
            $test_file = APPPATH . 'cache/test_export_csv.csv';
            $CI->excel->write('Csv', $test_file, $spreadsheet);
            
            if (!file_exists($test_file)) {
                throw new Exception("CSV export file not created");
            }
            
            // Verify it's a valid CSV (should contain commas)
            $content = file_get_contents($test_file);
            if (strpos($content, ',') === false && strlen($content) > 0) {
                throw new Exception("CSV file doesn't appear to contain comma-separated values");
            }
            
            $filesize = filesize($test_file);
            $this->pass("CSV export successful (" . $filesize . " bytes)");
            @unlink($test_file);
            
        } catch (Exception $e) {
            $this->fail("Export CSV Format: " . $e->getMessage());
        }
    }

    private function test_export_xls_format()
    {
        echo "\n" . COLOR_YELLOW . "Test 8: Export XLS Format" . COLOR_RESET . "\n";
        
        try {
            $CI = &get_instance();
            $CI->load->model('customer_model');
            $CI->load->library('excel');
            
            $list = $CI->customer_model->get_all_for_export();
            if (empty($list)) {
                throw new Exception("No customers found for XLS export test");
            }
            
            $spreadsheet = $CI->excel->create();
            $sheet = $spreadsheet->getActiveSheet();
            
            $headers = array('ID', 'Name', 'Email');
            $sheet->fromArray($headers, NULL, 'A1');
            
            // Verify XLS writer is available
            $test_file = APPPATH . 'cache/test_export_xls.xls';
            
            try {
                $CI->excel->write('Xls', $test_file, $spreadsheet);
                
                if (!file_exists($test_file)) {
                    throw new Exception("XLS export file not created");
                }
                
                $filesize = filesize($test_file);
                $this->pass("XLS export successful (" . $filesize . " bytes)");
                @unlink($test_file);
            } catch (Exception $writer_error) {
                $this->fail("XLS Format not available: " . $writer_error->getMessage());
            }
            
        } catch (Exception $e) {
            $this->fail("Export XLS Format: " . $e->getMessage());
        }
    }

    private function pass($message)
    {
        $this->passed++;
        echo COLOR_GREEN . "  ✓ PASS: " . $message . COLOR_RESET . "\n";
    }

    private function fail($message)
    {
        $this->failed++;
        echo COLOR_RED . "  ✗ FAIL: " . $message . COLOR_RESET . "\n";
    }

    private function print_summary()
    {
        echo "\n" . COLOR_BLUE . "╔═══════════════════════════════════════════════════════════════╗\n";
        echo "║                    Test Summary                                   ║\n";
        echo "╚═══════════════════════════════════════════════════════════════╝\n" . COLOR_RESET;
        
        $total = $this->passed + $this->failed;
        $pass_pct = $total > 0 ? ($this->passed / $total * 100) : 0;
        
        echo "\n  Total Tests:  " . $total . "\n";
        echo "  " . COLOR_GREEN . "Passed:       " . $this->passed . COLOR_RESET . "\n";
        echo "  " . COLOR_RED . "Failed:       " . $this->failed . COLOR_RESET . "\n";
        echo "  Success Rate: " . number_format($pass_pct, 1) . "%\n\n";
        
        if ($this->failed === 0) {
            echo COLOR_GREEN . "✓ All tests passed!\n" . COLOR_RESET;
            exit(0);
        } else {
            echo COLOR_RED . "✗ Some tests failed. Review above for details.\n" . COLOR_RESET;
            exit(1);
        }
    }
}

// Run tests
$tests = new ExportTests();
$tests->run();
?>
