<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

define('BASEPATH', dirname(__FILE__) . '/system/');
define('APPPATH', dirname(__FILE__) . '/application/');
define('ENVIRONMENT', 'development');

try {
    echo "① Loading CodeIgniter core...\n";
    require_once 'system/core/CodeIgniter.php';
    echo "✓ CodeIgniter core loaded successfully!\n";
} catch (Throwable $e) {
    echo "✗ Error during CI bootstrap:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n";
    echo $e->getTraceAsString();
}
?>
