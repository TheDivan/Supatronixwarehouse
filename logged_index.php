<?php
// Log all errors to file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Enable all error reporting 
error_reporting(E_ALL);
ini_set('display_errors', '0');  // Don't display to output

// Include the original index.php which sets up paths and constants
include 'index.php';
?>