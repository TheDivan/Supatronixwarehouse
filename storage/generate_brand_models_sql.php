<?php
// Generates a SQL file with INSERTs for Samsung models using the list in import_samsung_models.php
$src = __DIR__ . '/import_samsung_models.php';
$out = __DIR__ . '/../database/brand_models_samsung_inserts.sql';
$text = file_get_contents($src);
if ($text === false) {
    echo "Could not read $src\n";
    exit(1);
}
// Extract the array contents between $models = array( and );
if (!preg_match('/\$models\s*=\s*array\((.*?)\);/s', $text, $m)) {
    echo "Could not find models array in import_samsung_models.php\n";
    exit(1);
}
$arr_text = $m[1];
// Remove PHP single-line comments
$arr_text = preg_replace('/\/\/.*$/m', '', $arr_text);
// Split on commas that are followed by optional whitespace and a single quote
$parts = preg_split("/,(?=\s*')/", $arr_text);
$models = array();
foreach ($parts as $p) {
    $p = trim($p);
    if ($p === '') continue;
    // remove trailing commas
    $p = preg_replace('/,$/', '', $p);
    // strip surrounding single quotes if present
    if (preg_match("/^'((?:\\'|[^'])*)'$/", $p, $mm)) {
        $val = str_replace("'", "''", $mm[1]);
        $val = trim($val);
        if ($val !== '') $models[] = $val;
    }
}
if (count($models) === 0) {
    echo "No models found to write.\n";
    exit(1);
}
$lines = array();
$lines[] = "-- Samsung brand models SQL generated from storage/import_samsung_models.php";
$lines[] = "INSERT INTO `brand_models` (`id`, `brand_id`, `title`) VALUES";
$i = 1;
foreach ($models as $idx => $title) {
    $id = $i + $idx;
    $lines[] = "($id, 4, '" . $title . "')" . ($idx === count($models)-1 ? ";" : ",");
}
$content = implode("\n", $lines) . "\n";
file_put_contents($out, $content);
echo "Wrote " . strlen($content) . " bytes to $out\n";
exit(0);
