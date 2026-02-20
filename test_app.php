<?php
// Suppress any output before proper response handling
ob_start();

// Define constants exactly as index.php does
define('CI_ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', CI_ENVIRONMENT);
}

$_SERVER['CI_ENV'] = 'development';

// Set paths (check if running from web root or direct)
$base_path = realpath(__DIR__);
define('FCPATH', $base_path . DIRECTORY_SEPARATOR);
define('SYSTEMPATH', FCPATH . 'system' . DIRECTORY_SEPARATOR);
define('APPPATH', FCPATH . 'application' . DIRECTORY_SEPARATOR);
define('BASEPATH', SYSTEMPATH);
define('VIEWPATH', APPPATH . 'views' . DIRECTORY_SEPARATOR);

// Error to exception converter for cleaner output
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Skip deprecation warnings for this test
    if ($errno === E_DEPRECATED || $errno === 8192) {
        return true;
    }
    // Throw exception for serious errors
    if ($errno & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR)) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    return false;
});

try {
    // Load CodeIgniter
    require_once(BASEPATH . 'core/CodeIgniter.php');
    \ob_end_flush();
    echo "\n\n<!-- APP LOADED SUCCESSFULLY -->\n";
} catch (Throwable $e) {
    ob_clean();
    http_response_code(500);
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
?>
