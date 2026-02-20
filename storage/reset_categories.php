<?php
$host='localhost'; $user='root'; $pass=''; $db='bookingsoftware'; $port=3306;
$mysqli = new mysqli($host,$user,$pass,$db,$port);
if ($mysqli->connect_errno) { echo "Connect failed: " . $mysqli->connect_error . PHP_EOL; exit(1); }
$canonical = array('Screen/LCD','Touch','Button','Sim Tray','Battery','Speaker','Charging Unit/Block','Software','Back Plate','Camera Glass');
// ensure table exists
$mysqli->query("CREATE TABLE IF NOT EXISTS `stock_categories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `sort_order` int(10) DEFAULT 0,
  `created_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
// ensure sort_order column
$mysqli->query("ALTER TABLE `stock_categories` ADD COLUMN IF NOT EXISTS `sort_order` int(10) DEFAULT 0");
$mysqli->begin_transaction();
foreach ($canonical as $i => $name) {
    $nameEsc = $mysqli->real_escape_string($name);
    $res = $mysqli->query("SELECT id FROM stock_categories WHERE LOWER(name) = LOWER('".$nameEsc."') LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) {
        $id = (int)$row['id'];
        $mysqli->query("UPDATE stock_categories SET name='".$nameEsc."', sort_order=".$i." WHERE id=".$id);
    } else {
        $mysqli->query("INSERT INTO stock_categories (name,sort_order,created_datetime) VALUES ('".$nameEsc."',".$i.",NOW())");
    }
}
// delete others
$list = array(); foreach ($canonical as $c) $list[] = "'".$mysqli->real_escape_string(strtolower($c))."'";
$in = implode(',', $list);
$mysqli->query("DELETE FROM stock_categories WHERE LOWER(name) NOT IN ($in)");
$mysqli->commit();
echo "Categories reset to canonical list.\n";
$mysqli->close();
