<?php
$host='localhost'; $user='root'; $pass=''; $db='bookingsoftware'; $port=3306;
$mysqli = new mysqli($host,$user,$pass,$db,$port);
if ($mysqli->connect_errno) { echo "Connect failed: " . $mysqli->connect_error . PHP_EOL; exit(1); }
$canonical = array('Screen/LCD','Touch','Button','Sim Tray','Battery','Speaker','Charging Unit/Block','Software','Back Plate','Camera Glass');

function map_to_canonical($s, $canonical) {
    $low = strtolower(trim((string)$s));
    if ($low === '') return 'Software';
    // explicit rules
    if (strpos($low,'screen') !== false || strpos($low,'lcd') !== false) return 'Screen/LCD';
    if (strpos($low,'touch') !== false || strpos($low,'digitizer') !== false) return 'Touch';
    if (strpos($low,'button') !== false) return 'Button';
    if (strpos($low,'sim') !== false) return 'Sim Tray';
    if (strpos($low,'battery') !== false) return 'Battery';
    if (strpos($low,'speaker') !== false) return 'Speaker';
    if (strpos($low,'charg') !== false) return 'Charging Unit/Block';
    if (strpos($low,'software') !== false || strpos($low,'firm') !== false) return 'Software';
    if (strpos($low,'back') !== false) return 'Back Plate';
    if (strpos($low,'camera') !== false || strpos($low,'glass') !== false) return 'Camera Glass';
    // fallback: choose best levenshtein match against canonical (normalized)
    $norm = preg_replace('/[^a-z0-9]+/', '', $low);
    $best = null; $bestDist = PHP_INT_MAX;
    foreach ($canonical as $c) {
        $cNorm = preg_replace('/[^a-z0-9]+/', '', strtolower($c));
        $d = levenshtein($norm, $cNorm);
        if ($d < $bestDist) { $bestDist = $d; $best = $c; }
    }
    // threshold: allow up to 15% of length or 2
    $threshold = max(2, (int)floor(max(strlen($norm),1) * 0.15));
    if ($bestDist <= $threshold) return $best;
    return 'Software';
}

// 1) Update stock.part_category values to canonical
$res = $mysqli->query("SELECT id, part_category FROM stock");
$updated = 0;
while ($row = $res->fetch_assoc()) {
    $id = (int)$row['id'];
    $old = $row['part_category'] ?? '';
    $new = map_to_canonical($old, $canonical);
    if ($new !== $old) {
        $mysqli->query("UPDATE stock SET part_category='".$mysqli->real_escape_string($new)."' WHERE id=".$id);
        $updated++;
    }
}

// 2) Rebuild stock_categories table to exactly canonical list (preserve created_datetime for matches)
// ensure table exists
$mysqli->query("CREATE TABLE IF NOT EXISTS stock_categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(128) DEFAULT NULL,
  sort_order INT DEFAULT 0,
  created_datetime DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
// Upsert each canonical name
$inserted = 0; $kept = 0;
foreach ($canonical as $i => $name) {
    $stmt = $mysqli->prepare('SELECT id FROM stock_categories WHERE LOWER(name)=LOWER(?) LIMIT 1');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $r = $stmt->get_result();
    if ($r && $r->num_rows) {
        $row = $r->fetch_assoc();
        $id = (int)$row['id'];
        $mysqli->query("UPDATE stock_categories SET name='".$mysqli->real_escape_string($name)."', sort_order=".(int)$i." WHERE id=".$id);
        $kept++;
    } else {
        $mysqli->query("INSERT INTO stock_categories (name, sort_order, created_datetime) VALUES ('".$mysqli->real_escape_string($name)."',".(int)$i." ,'".date('Y-m-d H:i:s')."')");
        $inserted++;
    }
}
// Remove any categories not in canonical (case-insensitive)
$esc = array(); foreach ($canonical as $c) $esc[] = "'".$mysqli->real_escape_string(strtolower($c))."'";
$inClause = implode(',', $esc);
$mysqli->query("DELETE FROM stock_categories WHERE LOWER(name) NOT IN ($inClause)");

echo "Stock rows updated: $updated\n";
echo "Categories inserted: $inserted, existing kept: $kept\n";
$mysqli->close();
