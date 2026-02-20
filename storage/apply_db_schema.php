<?php
// CLI helper to apply the SQL in database/db.sql to the configured DB.
// Usage: php storage/apply_db_schema.php
if (PHP_SAPI !== 'cli') {
    echo "This script must be run from the CLI\n";
    exit(1);
}

// Load DB config without triggering CodeIgniter's direct-access guard
$db_config_file = __DIR__ . '/../application/config/database.php';
if (!file_exists($db_config_file)) {
    echo "Cannot find database config: $db_config_file\n";
    exit(1);
}

// Some CI config files protect against direct access using defined('BASEPATH') checks.
// Define a dummy BASEPATH constant so the file can be required in CLI context.
if (!defined('BASEPATH')) define('BASEPATH', true);
if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'development');
require $db_config_file;
if (!isset($db) || !isset($active_group)) {
    echo "Invalid database config\n";
    exit(1);
}

$cfg = $db[$active_group];
$host = $cfg['hostname'];
$user = $cfg['username'];
$pass = $cfg['password'];
$name = $cfg['database'];
$port = isset($cfg['port']) ? $cfg['port'] : 3306;

// Parse CLI overrides: --host, --user, --pass, --db, --port
$cli_opts = getopt('', array('host::','user::','pass::','db::','port::'));
if (!empty($cli_opts)) {
    if (!empty($cli_opts['host'])) $host = $cli_opts['host'];
    if (!empty($cli_opts['user'])) $user = $cli_opts['user'];
    if (array_key_exists('pass', $cli_opts)) $pass = $cli_opts['pass'];
    if (!empty($cli_opts['db'])) $name = $cli_opts['db'];
    if (!empty($cli_opts['port'])) $port = (int)$cli_opts['port'];
}
$port = isset($cfg['port']) ? $cfg['port'] : 3306;

// Parse CLI overrides: --host, --user, --pass, --db, --port
$options = getopt('', array('host::', 'user::', 'pass::', 'db::', 'port::'));
if ($options !== false) {
    if (!empty($options['host'])) $host = $options['host'];
    if (!empty($options['user'])) $user = $options['user'];
    if (array_key_exists('pass', $options)) $pass = $options['pass'];
    if (!empty($options['db'])) $name = $options['db'];
    if (!empty($options['port'])) $port = (int)$options['port'];
}

$sql_file = __DIR__ . '/../database/db.sql';
if (!file_exists($sql_file)) {
    echo "Cannot find SQL file: $sql_file\n";
    exit(1);
}

// Disable mysqli exceptions so we can handle errors and continue where possible.
mysqli_report(MYSQLI_REPORT_OFF);
echo "Connecting to {$host}:{$port} as {$user}...\n";
$mysqli = new mysqli($host, $user, $pass, $name, $port);
if ($mysqli->connect_errno) {
    echo "Connect failed: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "\n";
    exit(1);
}

$sql = file_get_contents($sql_file);
if ($sql === false) {
    echo "Failed reading SQL file\n";
    exit(1);
}

// Run as multi_query so the entire dump can be applied.
echo "Applying SQL from database/db.sql ... this may take a while.\n";
if (!$mysqli->multi_query($sql)) {
    echo "multi_query error: (" . $mysqli->errno . ") " . $mysqli->error . "\n";
    exit(1);
}

$success = 0;
$failed = 0;
// Error numbers we can safely ignore and continue past.
$ignore_errnos = array(1050, 1060);

// Consume results and check for errors after each statement set
while (true) {
    if ($res = $mysqli->store_result()) {
        $res->free();
        $success++;
    } else {
        if ($mysqli->errno) {
            if (in_array($mysqli->errno, $ignore_errnos)) {
                echo "Ignored SQL error ({$mysqli->errno}): {$mysqli->error}\n";
                $success++;
            } else {
                echo "Statement error: ({$mysqli->errno}) {$mysqli->error}\n";
                $failed++;
            }
        } else {
            $success++;
        }
    }

    if (!$mysqli->more_results()) break;

    // Advance to next result; if it fails we'll inspect errno and continue.
    if (!$mysqli->next_result()) {
        if ($mysqli->errno) {
            if (in_array($mysqli->errno, $ignore_errnos)) {
                echo "Ignored next_result SQL error ({$mysqli->errno}): {$mysqli->error}\n";
            } else {
                echo "Next result error: ({$mysqli->errno}) {$mysqli->error}\n";
                $failed++;
            }
        }
    }
}

echo "SQL apply finished. Success sets: {$success}, failed sets: {$failed}\n";
$mysqli->close();
exit(0);
