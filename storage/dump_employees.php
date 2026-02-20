<?php
$host='localhost'; $user='root'; $pass=''; $db='bookingsoftware'; $port=3306;
$mysqli = new mysqli($host,$user,$pass,$db,$port);
if ($mysqli->connect_errno) { echo "Connect failed: " . $mysqli->connect_error . PHP_EOL; exit(1); }
$tables = array('employee','employees');
foreach ($tables as $t) {
    $res = $mysqli->query("SHOW TABLES LIKE '".$t."'");
    if ($res && $res->num_rows>0) {
        echo "Found table: $t\n";
        $r = $mysqli->query("SELECT * FROM $t LIMIT 20");
        while ($row = $r->fetch_assoc()) {
            echo json_encode($row) . "\n";
        }
    }
}
$mysqli->close();
