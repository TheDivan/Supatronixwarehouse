<?php
$host='localhost'; $user='root'; $pass=''; $db='bookingsoftware'; $port=3306;
$mysqli = new mysqli($host,$user,$pass,$db,$port);
if ($mysqli->connect_errno) { echo "Connect failed: " . $mysqli->connect_error . PHP_EOL; exit(1); }
$canonical = array('Screen/LCD','Touch','Button','Sim Tray','Battery','Speaker','Charging Unit/Block','Software','Back Plate','Camera Glass');
function map_category($s, $canonical) {
    $low = strtolower(trim($s));
    if ($low === '') return 'Software';
    // rules
    if (strpos($low,'screen') !== false || strpos($low,'lcd') !== false) return 'Screen/LCD';
    if (strpos($low,'touch') !== false || strpos($low,'digitizer') !== false || strpos($low,'tuuch') !== false) return 'Touch';
    if (strpos($low,'button') !== false) return 'Button';
    if (strpos($low,'sim') !== false) return 'Sim Tray';
    if (strpos($low,'battery') !== false) return 'Battery';
    if (strpos($low,'speaker') !== false) return 'Speaker';
    if (strpos($low,'charg') !== false) return 'Charging Unit/Block';
    if (strpos($low,'software') !== false || strpos($low,'firm') !== false) return 'Software';
    if (strpos($low,'back') !== false) return 'Back Plate';
    if (strpos($low,'camera') !== false || strpos($low,'glass') !== false) return 'Camera Glass';
    // fallback: if matches any canonical substring
    foreach ($canonical as $c) {
        if (strpos(strtolower($c), $low) !== false || strpos($low, strtolower($c)) !== false) return $c;
    }
    return 'Software';
}

$res = $mysqli->query("SELECT id, part_category FROM stock");
$updated = 0;
while ($row = $res->fetch_assoc()) {
    $id = (int)$row['id'];
    $cat = $row['part_category'] ?? '';
    $new = map_category($cat, $canonical);
    if ($new !== $cat) {
        $mysqli->query("UPDATE stock SET part_category='".$mysqli->real_escape_string($new)."' WHERE id=".$id);
        $updated++;
    }
}
echo "Normalized categories for $updated stock items.\n";
$mysqli->close();
