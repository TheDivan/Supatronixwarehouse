<?php
// Run from project root: php storage/import_samsung_models.php
// Make CI config loadable by defining BASEPATH so the config file doesn't exit.
if (!defined('BASEPATH')) define('BASEPATH', true);
if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'development');
// Inserts Samsung models into `brand_models` table (brand_id = 4)

include __DIR__ . '/../application/config/database.php';
$dbconf = isset($db['default']) ? $db['default'] : null;
if (!$dbconf) {
    echo "Could not read database configuration from application/config/database.php\n";
    exit(1);
}
$mysqli = new mysqli($dbconf['hostname'], $dbconf['username'], $dbconf['password'], $dbconf['database']);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "\n";
    exit(1);
}
$mysqli->set_charset('utf8');

$models = array(
    // Galaxy S series
    'Galaxy S6', 'Galaxy S6 Edge', 'Galaxy S6 Edge+', 'Galaxy S7', 'Galaxy S7 Edge',
    'Galaxy S8', 'Galaxy S8+', 'Galaxy S9', 'Galaxy S9+', 'Galaxy S10e', 'Galaxy S10', 'Galaxy S10+', 'Galaxy S10 5G',
    'Galaxy S20', 'Galaxy S20+', 'Galaxy S20 Ultra', 'Galaxy S20 FE',
    'Galaxy S21', 'Galaxy S21+', 'Galaxy S21 Ultra', 'Galaxy S21 FE',
    'Galaxy S22', 'Galaxy S22+', 'Galaxy S22 Ultra',
    'Galaxy S23', 'Galaxy S23+', 'Galaxy S23 Ultra', 'Galaxy S23 FE',
    'Galaxy S24', 'Galaxy S24+', 'Galaxy S24 Ultra', 'Galaxy S24 FE',
    'Galaxy S25', 'Galaxy S25+', 'Galaxy S25 Ultra', 'Galaxy S25 Edge',
    // Note series
    'Galaxy Note 5', 'Galaxy Note 7', 'Galaxy Note 8', 'Galaxy Note 9', 'Galaxy Note 10', 'Galaxy Note 10+', 'Galaxy Note 20', 'Galaxy Note 20 Ultra',
    // Foldables
    'Galaxy Fold (1st Gen)', 'Galaxy Z Fold 2', 'Galaxy Z Fold 3', 'Galaxy Z Fold 4', 'Galaxy Z Fold 5', 'Galaxy Z Fold 6', 'Galaxy Z Fold 7',
    'Galaxy Z Flip', 'Galaxy Z Flip 5G', 'Galaxy Z Flip 3', 'Galaxy Z Flip 4', 'Galaxy Z Flip 5', 'Galaxy Z Flip 6', 'Galaxy Z Flip 7',
    // Galaxy A series (selected)
    'Galaxy A3', 'Galaxy A5', 'Galaxy A7', 'Galaxy A6', 'Galaxy A6+', 'Galaxy A7 (2018)', 'Galaxy A8', 'Galaxy A8+',
    'Galaxy A10', 'Galaxy A20', 'Galaxy A30', 'Galaxy A40', 'Galaxy A50', 'Galaxy A70', 'Galaxy A80',
    'Galaxy A01', 'Galaxy A11', 'Galaxy A21', 'Galaxy A21s', 'Galaxy A31', 'Galaxy A41', 'Galaxy A51', 'Galaxy A51 5G', 'Galaxy A71', 'Galaxy A71 5G',
    'Galaxy A02', 'Galaxy A02s', 'Galaxy A12', 'Galaxy A22', 'Galaxy A22 5G', 'Galaxy A32', 'Galaxy A32 5G', 'Galaxy A52', 'Galaxy A52 5G', 'Galaxy A52s 5G', 'Galaxy A72',
    'Galaxy A03', 'Galaxy A03 Core', 'Galaxy A03s', 'Galaxy A13', 'Galaxy A13 5G', 'Galaxy A23', 'Galaxy A33 5G', 'Galaxy A53 5G', 'Galaxy A73 5G',
    'Galaxy A04', 'Galaxy A04e', 'Galaxy A04s', 'Galaxy A14', 'Galaxy A14 5G', 'Galaxy A24', 'Galaxy A34 5G', 'Galaxy A54 5G', 'Galaxy A05', 'Galaxy A05s',
    'Galaxy A15', 'Galaxy A15 5G', 'Galaxy A25 5G', 'Galaxy A35 5G', 'Galaxy A55 5G', 'Galaxy A06', 'Galaxy A16', 'Galaxy A16 5G', 'Galaxy A26 5G', 'Galaxy A36 5G', 'Galaxy A56 5G',
    // Galaxy J / M / other series
    'Galaxy J1', 'Galaxy J1 Ace', 'Galaxy J2', 'Galaxy J5', 'Galaxy J7',
    'Galaxy J1 (2016)', 'Galaxy J2 (2016)', 'Galaxy J3 (2016)', 'Galaxy J5 (2016)', 'Galaxy J7 (2016)',
    'Galaxy J2 Pro', 'Galaxy J3 (2017)', 'Galaxy J5 (2017)', 'Galaxy J7 (2017)', 'Galaxy J7 Pro', 'Galaxy J4', 'Galaxy J4+', 'Galaxy J6', 'Galaxy J6+', 'Galaxy J8',
    'Galaxy M12', 'Galaxy M32', 'Galaxy M13', 'Galaxy M23 5G', 'Galaxy M33 5G', 'Galaxy M14', 'Galaxy M14 5G', 'Galaxy M34 5G', 'Galaxy M15 5G', 'Galaxy M35 5G', 'Galaxy M55 5G'
);

$brand_id = 4; // Samsung

$inserted = 0;
$skipped = 0;

$check_stmt = $mysqli->prepare("SELECT id FROM brand_models WHERE brand_id = ? AND title = ? LIMIT 1");
$ins_stmt = $mysqli->prepare("INSERT INTO brand_models (brand_id, title) VALUES (?, ?)");
if (!$check_stmt || !$ins_stmt) {
    echo "Prepare failed: (".$mysqli->errno.") " . $mysqli->error . "\n";
    exit(1);
}

foreach ($models as $title) {
    $t = trim($title);
    $check_stmt->bind_param('is', $brand_id, $t);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        $skipped++;
        continue;
    }
    $ins_stmt->bind_param('is', $brand_id, $t);
    if ($ins_stmt->execute()) {
        $inserted++;
    } else {
        echo "Failed to insert '".$t."': (".$ins_stmt->errno.") ".$ins_stmt->error."\n";
    }
}

echo "Inserted: $inserted\n";
echo "Skipped (already existed): $skipped\n";

$check_stmt->close();
$ins_stmt->close();
$mysqli->close();

exit(0);
