<?php
// Minimal test to debug exact error
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development');
}
if (!defined('BASEPATH')) {
    define('BASEPATH', dirname(__FILE__) . '/system/');
}
if (!defined('APPPATH')) {
    define('APPPATH', dirname(__FILE__) . '/application/');
}

require_once('application/config/database.php');

echo "PHP Version: " . phpversion() . "\n";
echo "Database config hostname: " . $db['default']['hostname'] . "\n";
echo "Database config username: " . $db['default']['username'] . "\n";
echo "Database config database: " . $db['default']['database'] . "\n";

// Try to connect
try {
    $mysqli = new mysqli(
        $db['default']['hostname'],
        $db['default']['username'],
        $db['default']['password'],
        $db['default']['database']
    );
    if ($mysqli->connect_error) {
        echo "Connection Error (" . $mysqli->connect_errno . "): " . $mysqli->connect_error . "\n";
    } else {
        echo "Database connection successful!\n";
        echo "Server info: " . $mysqli->server_info . "\n";
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
?>
