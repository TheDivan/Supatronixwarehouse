<?php
/**
 * Router for PHP Built-in Server
 * Captures and logs errors for debugging
 */

// Only route requests to actual files/directories, pass everything else to index.php
if (preg_match('/\.(?:jpg|jpeg|gif|png|txt|css|js|map|svg|woff|ttf|eot)(\.map)?$/i', $_SERVER["REQUEST_URI"])) {
    return false;    // Serve the requested resource as-is
}

// Log request
error_log('[' . date('Y-m-d H:i:s') . '] ' . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI']);

// Try to load index.php
try {
    // Set error handler to throw exceptions
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        if ($errno & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR)) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        }
        return false;
    });

    // Include index.php
    include 'index.php';
    
} catch (Throwable $e) {
    error_log('EXCEPTION: ' . $e->getMessage() . ' in ' . basename($e->getFile()) . ':' . $e->getLine());
    http_response_code(500);
    echo "Internal Server Error\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
?>
