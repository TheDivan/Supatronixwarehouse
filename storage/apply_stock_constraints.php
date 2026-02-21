<?php
// Safe migration to add stock constraints and helpful columns
if (PHP_SAPI !== 'cli') { echo "Run from CLI\n"; exit(1); }
if (!defined('BASEPATH')) define('BASEPATH', true);
require __DIR__ . '/../application/config/database.php';
$cfg = $db[$active_group];
$host = $cfg['hostname']; $user = $cfg['username']; $pass = $cfg['password']; $name = $cfg['database']; $port = isset($cfg['port']) ? $cfg['port'] : 3306;
$options = getopt('', array('host::','user::','pass::','db::','port::'));
if ($options !== false) {
    if (!empty($options['host'])) $host = $options['host'];
    if (!empty($options['user'])) $user = $options['user'];
    if (array_key_exists('pass', $options)) $pass = $options['pass'];
    if (!empty($options['db'])) $name = $options['db'];
    if (!empty($options['port'])) $port = (int)$options['port'];
}
$mysqli = new mysqli($host,$user,$pass,$name,$port);
if ($mysqli->connect_errno) { echo "DB connect failed: " . $mysqli->connect_error . "\n"; exit(1); }
$queries = array();
// Add helpful columns if missing
$queries[] = "ALTER TABLE `stock` ADD COLUMN IF NOT EXISTS `device_model` varchar(128) DEFAULT NULL";
$queries[] = "ALTER TABLE `stock` ADD COLUMN IF NOT EXISTS `supplier_id` int(10) DEFAULT NULL";
$queries[] = "ALTER TABLE `stock` ADD COLUMN IF NOT EXISTS `created_by` int(10) DEFAULT NULL";
$queries[] = "ALTER TABLE `stock` ADD COLUMN IF NOT EXISTS `created_datetime` datetime DEFAULT NULL";
$queries[] = "ALTER TABLE `stock` MODIFY COLUMN `quantity` int(10) NOT NULL DEFAULT 0";
$queries[] = "ALTER TABLE `stock` MODIFY COLUMN `cost` decimal(10,2) DEFAULT NULL";
// Add unique index to avoid duplicates (best-effort). Use IGNORE if exists.
$queries[] = "CREATE UNIQUE INDEX IF NOT EXISTS uq_stock_unique ON `stock` (`part_name`,`part_category`,`device_model`,`supplier_id`,`office_id`)";
// Ensure supplier FK exists
$queries[] = "ALTER TABLE `stock` ADD CONSTRAINT IF NOT EXISTS fk_stock_supplier FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE SET NULL";
// Ensure office FK exists
$queries[] = "ALTER TABLE `stock` ADD CONSTRAINT IF NOT EXISTS fk_stock_office FOREIGN KEY (`office_id`) REFERENCES `store_locations`(`id`) ON DELETE SET NULL";
// Ensure part_category not null: only if there are no NULLs
$res = $mysqli->query("SELECT COUNT(*) as c FROM `stock` WHERE part_category IS NULL OR part_category = ''");
$nullCount = ($res && ($r=$res->fetch_assoc())) ? (int)$r['c'] : 0;
if ($nullCount === 0) {
    $queries[] = "ALTER TABLE `stock` MODIFY COLUMN `part_category` varchar(64) NOT NULL";
}

foreach ($queries as $q) {
    // Some MySQL versions don't support IF NOT EXISTS on ALTER/INDEX creation; run and ignore errors
    printf("Running: %s\n", $q);
    if (!$mysqli->query($q)) {
        printf("Ignored/failed: (%d) %s\n", $mysqli->errno, $mysqli->error);
    }
}
$mysqli->close();
echo "Migration attempt complete. Review output for any errors.\n";
exit(0);
