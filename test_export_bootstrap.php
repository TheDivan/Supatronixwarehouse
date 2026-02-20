<?php
/**
 * Test Bootstrap for Export Integration Tests
 * Initializes CodeIgniter without triggering any controller actions
 */

// Prevent output to avoid header errors
ob_start();

define('BASEPATH', dirname(__FILE__) . '/system/');
define('APPPATH', dirname(__FILE__) . '/application/');
define('FCPATH', dirname(__FILE__) . '/');

// Suppress any output
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', '0');

// Check if Composer autoload exists
if (!file_exists(APPPATH . '../vendor/autoload.php')) {
    die("ERROR: Composer autoload not found. Run 'composer install'.\n");
}

require_once(BASEPATH . 'core/CodeIgniter.php');

// Clean up output buffer  
ob_end_clean();

// Verify we have database connection
$CI = &get_instance();
if (!$CI->db) {
    die("ERROR: Database connection failed.\n");
}

// Suppress all output going forward
ob_start();
?>
