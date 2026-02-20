<?php
$host = 'localhost'; $port = 3306; $user = 'root'; $pass = ''; $db = 'bookingsoftware';
$mysqli = @new mysqli($host, $user, $pass, $db, $port);
if ($mysqli->connect_errno) { echo "CONNECT_ERROR: (".$mysqli->connect_errno.") ".$mysqli->connect_error.PHP_EOL; exit(1); }

function table_exists($mysqli, $db, $table) {
    $res = $mysqli->query("SELECT COUNT(*) c FROM information_schema.tables WHERE table_schema='".$mysqli->real_escape_string($db)."' AND table_name='".$mysqli->real_escape_string($table)."'");
    if (!$res) return false; $r = $res->fetch_assoc(); return ($r['c']>0);
}

function column_exists($mysqli, $db, $table, $col) {
    $res = $mysqli->query("SELECT COUNT(*) c FROM information_schema.columns WHERE table_schema='".$mysqli->real_escape_string($db)."' AND table_name='".$mysqli->real_escape_string($table)."' AND column_name='".$mysqli->real_escape_string($col)."'");
    if (!$res) return false; $r = $res->fetch_assoc(); return ($r['c']>0);
}

// create api_tokens if missing
if (!table_exists($mysqli, $db, 'api_tokens')) {
    $sql = "CREATE TABLE `api_tokens` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) DEFAULT NULL,
  `token` varchar(128) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    if ($mysqli->query($sql)) echo "Created api_tokens\n"; else echo "Failed creating api_tokens: " . $mysqli->error . "\n";
} else echo "api_tokens exists\n";

// add supplier_id to stock if missing
if (table_exists($mysqli,$db,'stock') && !column_exists($mysqli,$db,'stock','supplier_id')) {
    $sql = "ALTER TABLE `stock` ADD COLUMN `supplier_id` int(10) DEFAULT NULL";
    if ($mysqli->query($sql)) echo "Added stock.supplier_id\n"; else echo "Failed adding supplier_id: " . $mysqli->error . "\n";
} else echo "stock.supplier_id exists or stock table missing\n";

$mysqli->close();
echo "Done\n";
