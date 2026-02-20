#!/usr/bin/env php
<?php
/**
 * Simple Export Functionality Test
 * Tests export endpoints without complex bootstrap issues
 */

$base_path = dirname(__FILE__);
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Manual bootstrap
define('BASEPATH', $base_path . '/system/');
define('APPPATH', $base_path . '/application/');
define('FCPATH', $base_path. '/');
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

// Load composer autoload
if (!file_exists($base_path . '/vendor/autoload.php')) {
    echo "FAIL: Missing vendor/autoload.php. Run: composer install\n";
    exit(1);
}

require $base_path . '/vendor/autoload.php';

// Set up error handling
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (strpos($errfile, 'direct_test.php') === false && 
        strpos($errstr, 'headers') === false &&
        strpos($errstr, 'Cannot modify') === false) {
        echo "ERROR [$errno]: $errstr in $errfile:$errline\n";
    }
});

// Redirect output to prevent header issues
ob_start();

try {
    // Initialize CodeIgniter
    require $base_path . '/system/core/CodeIgniter.php';
} catch (Exception $e) {
    ob_end_clean();
    echo "BOOTSTRAP FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

ob_end_clean();

// Verify we can access CI
try {
    $CI = &get_instance();
    echo "✓ Bootstrap successful\n";
    echo "✓ Database: " . ($CI->db ? "CONNECTED" : "FAILED") . "\n";
} catch (Exception $e) {
    echo "FAIL: Cannot get CI instance: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 1: Load Excel library
echo "\n=== Test 1: Load Excel Library ===\n";
try {
    $CI->load->library('excel');
    echo "✓ Excel library loaded\n";
} catch (Exception $e) {
    echo "✗ FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Create spreadsheet
echo "\n=== Test 2: Create Spreadsheet ===\n";
try {
    $sheet = $CI->excel->create();
    echo "✓ Spreadsheet created\n
";
} catch (Exception $e) {
    echo "✗ FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Load Job Model for export
echo "\n=== Test 3: Load Job Model ===\n";
try {
    $CI->load->model('job_model');
    $jobs = $CI->job_model->get_all_for_export();
    echo "✓ Job Model loaded, found " . count($jobs) . " jobs\n";
} catch (Exception $e) {
    echo "✗ FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 4: Load Customer Model for export
echo "\n=== Test 4: Load Customer Model ===\n";
try {
    $CI->load->model('customer_model');
    $customers = $CI->customer_model->get_all_for_export();
    echo "✓ Customer Model loaded, found " . count($customers) . " customers\n";
} catch (Exception $e) {
    echo "✗ FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 5: Load Email Model for export
echo "\n=== Test 5: Load Email Model ===\n";
try {
    $CI->load->model('email_model');
    $emails = $CI->email_model->get_all_for_export();
    echo "✓ Email Model loaded, found " . count($emails) . " email templates\n";
} catch (Exception $e) {
    echo "✗ FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 6: Export to XLSX
echo "\n=== Test 6: Export Jobs to XLSX ===\n";
try {
    $spreadsheet = $CI->excel->create();
    $active_sheet = $spreadsheet->getActiveSheet();
    $active_sheet->setCellValue('A1', 'ID');
    $active_sheet->setCellValue('B1', 'Technician');
    
    foreach ($jobs as $idx => $job) {
        $row = $idx + 2;
        $active_sheet->setCellValue('A' . $row, $job->id);
        $active_sheet->setCellValue('B' . $row, $job->technician);
        if ($idx >= 10) break; // Limit to first 10
    }
    
    $export_file = $base_path . '/application/cache/test_jobs_export.xlsx';
    @mkdir(dirname($export_file), 0777, true);
    $CI->excel->write('Xlsx', $export_file, $spreadsheet);
    
    if (!file_exists($export_file)) {
        echo "✗ FAIL: Export file not created\n";
        exit(1);
    }
    
    $size = filesize($export_file);
    echo "✓ XLSX export successful (" . number_format($size) . " bytes)\n";
    @unlink($export_file);
} catch (Exception $e) {
    echo "✗ FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 7: Export to CSV
echo "\n=== Test 7: Export Customers to CSV ===\n";
try {
    $spreadsheet = $CI->excel->create();
    $active_sheet = $spreadsheet->getActiveSheet();
    $active_sheet->setCellValue('A1', 'ID');
    $active_sheet->setCellValue('B1', 'Name');
    $active_sheet->setCellValue('C1', 'Email');
    
    foreach ($customers as $idx => $cust) {
        $row = $idx + 2;
        $active_sheet->setCellValue('A' . $row, $cust->id);
        $active_sheet->setCellValue('B' . $row, $cust->name);
        $active_sheet->setCellValue('C' . $row, $cust->email);
        if ($idx >= 10) break;
    }
    
    $export_file = $base_path . '/application/cache/test_customers_export.csv';
    @mkdir(dirname($export_file), 0777, true);
    $CI->excel->write('Csv', $export_file, $spreadsheet);
    
    if (!file_exists($export_file)) {
        echo "✗ FAIL: CSV export file not created\n";
        exit(1);
    }
    
    $size = filesize($export_file);
    echo "✓ CSV export successful (" . number_format($size) . " bytes)\n";
    @unlink($export_file);
} catch (Exception $e) {
    echo "✗ FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✓ ALL EXPORT TESTS PASSED\n";
echo str_repeat("=", 50) . "\n";
exit(0);
?>
