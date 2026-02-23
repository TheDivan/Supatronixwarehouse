<?php
/**
 * CLI migration to update customers.office_id using jobs table where customers.office_id is null or 0
 * Usage: php storage/update_customers_office_id.php
 */

if (PHP_SAPI !== 'cli') {
    echo "This script must be run from CLI only.\n";
    exit(1);
}

if (!defined('BASEPATH')) define('BASEPATH', TRUE);
if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'development');

require __DIR__ . '/../application/config/database.php';

if (!isset($db) || !isset($active_group)) {
    echo "Database configuration not found\n";
    exit(1);
}

$cfg = $db[$active_group];
$host = $cfg['hostname'];
$user = $cfg['username'];
$pass = $cfg['password'];
$name = $cfg['database'];
$port = isset($cfg['port']) ? $cfg['port'] : 3306;

$mysqli = new mysqli($host, $user, $pass, $name, $port);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "\n";
    exit(1);
}

echo "Connected to database: $name\n";

// Find customers where office_id is missing, zero, or mismatched with any of their jobs
$sql = "SELECT c.id as customer_id, c.office_id as current_office, j.office_id as job_office
        FROM customers c
        JOIN jobs j ON j.customer_id = c.id
        WHERE (c.office_id IS NULL OR c.office_id = 0 OR c.office_id <> j.office_id)
        GROUP BY c.id";

$res = $mysqli->query($sql);
if (!$res) {
    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error . "\n";
    exit(1);
}

$rows = $res->fetch_all(MYSQLI_ASSOC);
echo "Found " . count($rows) . " customers to update.\n";

$updated = 0;
foreach ($rows as $r) {
    $customer_id = (int)$r['customer_id'];
    $job_office = (int)$r['job_office'];
    $update_sql = "UPDATE customers SET office_id = ? WHERE id = ?";
    $stmt = $mysqli->prepare($update_sql);
    if ($stmt) {
        $stmt->bind_param('ii', $job_office, $customer_id);
        if ($stmt->execute()) {
            $updated++;
            echo "Updated customer id {$customer_id} -> office_id {$job_office}\n";
        } else {
            echo "Failed to update customer {$customer_id}: " . $stmt->error . "\n";
        }
        $stmt->close();
    } else {
        echo "Prepare failed: " . $mysqli->error . "\n";
    }
}

echo "Migration complete. Updated: {$updated}\n";
$mysqli->close();

?>