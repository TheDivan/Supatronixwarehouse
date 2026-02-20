<?php
// Set environment first
define('ENVIRONMENT', 'development');

// Set correct paths - MUST be set before CodeIgniter.php
define('FCPATH', __DIR__.DIRECTORY_SEPARATOR);
define('SYSTEMPATH', FCPATH.'system'.DIRECTORY_SEPARATOR);
define('APPPATH', FCPATH.'application'.DIRECTORY_SEPARATOR);
define('BASEPATH', SYSTEMPATH);
define('VIEWPATH', APPPATH.'views'.DIRECTORY_SEPARATOR);

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== BOOTSTRAP TEST WITH ENVIRONMENT ===\n";
echo "PHP Version: ".PHP_VERSION."\n";
echo "Environment: ".ENVIRONMENT."\n\n";

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo "[$errno] $errstr in ".basename($errfile).":$errline\n";
    return false;
}, E_ALL);

try {
    // Load CodeIgniter - this will load all core classes
    require_once SYSTEMPATH.'core/CodeIgniter.php';
    echo "Bootstrap successful!\n";
} catch (Throwable $e) {
    echo "EXCEPTION: ".$e->getMessage()."\n";
    echo "Type: ".get_class($e)."\n";
    echo "File: ".$e->getFile().":".$e->getLine()."\n";
    echo "\nTrace:\n";
    foreach($e->getTrace() as $i => $frame) {
        echo "  [$i] {$frame['function']}() in ".(isset($frame['file']) ? basename($frame['file']).':'.$frame['line'] : 'unknown')."\n";
    }
}

restore_error_handler();
?>
