<?php
$mysqli = new mysqli('localhost','root','','bookingsoftware');
if ($mysqli->connect_errno) { echo "Connect failed: " . $mysqli->connect_error . PHP_EOL; exit(1); }
$name = 'My New Category Test';
$stmt = $mysqli->prepare('SELECT id,name FROM stock_categories WHERE name = ? LIMIT 1');
$stmt->bind_param('s', $name);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows) {
    $row = $res->fetch_assoc();
    echo "FOUND\n";
    echo json_encode($row) . PHP_EOL;
} else {
    echo "NOTFOUND\n";
}
$mysqli->close();
