<?php
$host = 'localhost';
$port = 3306;
$user = 'root';
$pass = '';
$db = 'bookingsoftware';

$mysqli = @new mysqli($host, $user, $pass, $db, $port);
if ($mysqli->connect_errno) {
    echo "CONNECT_ERROR: (".$mysqli->connect_errno.") ".$mysqli->connect_error.PHP_EOL;
    exit(1);
}

$items = [
    'suppliers' => false,
    'stock_movements' => false,
    'api_tokens' => false,
    'stock_supplier_id' => false
];

// check tables
foreach (['suppliers','stock_movements','api_tokens'] as $t) {
    $res = $mysqli->query("SELECT COUNT(*) as c FROM information_schema.tables WHERE table_schema='".$mysqli->real_escape_string($db)."' AND table_name='".$mysqli->real_escape_string($t)."'");
    if ($res) {
        $row = $res->fetch_assoc();
        $items[$t] = ($row['c'] > 0);
    }
}
// check column supplier_id in stock
$res = $mysqli->query("SELECT COUNT(*) as c FROM information_schema.columns WHERE table_schema='".$mysqli->real_escape_string($db)."' AND table_name='stock' AND column_name='supplier_id'");
if ($res) {
    $row = $res->fetch_assoc();
    $items['stock_supplier_id'] = ($row['c'] > 0);
}

foreach ($items as $k => $v) {
    echo $k . ': ' . ($v ? 'OK' : 'MISSING') . PHP_EOL;
}
$mysqli->close();
return 0;
