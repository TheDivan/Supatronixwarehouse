<?php
// Run from CLI: php export_collections.php
if (php_sapi_name() !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

// Load DB config from CodeIgniter
require_once __DIR__ . '/../application/config/database.php';
if (!isset($db) || !isset($db['default'])) {
    echo "Could not load database configuration.\n";
    exit(1);
}
$cfg = $db['default'];
$host = $cfg['hostname'] ?? 'localhost';
$user = $cfg['username'] ?? '';
$pass = $cfg['password'] ?? '';
$name = $cfg['database'] ?? '';
$port = $cfg['port'] ?? null;

$mysqli = new mysqli($host, $user, $pass, $name, $port ?: 3306);
if ($mysqli->connect_errno) {
    echo "Failed to connect to DB: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "\n";
    exit(1);
}
// Determine format from CLI arg (json/csv/xlsx)
$format = $argv[1] ?? 'json';
$tables = array(
    'customers',
    'employees',
    'jobs',
    'job_items',
    'brands',
    'brand_models',
    'offices'
);

// Ensure exports folder exists
$outdir = __DIR__ . '/exports';
if (!is_dir($outdir)) mkdir($outdir, 0755, true);

if ($format === 'xlsx') {
    // write each table to its own sheet in one workbook
    require_once __DIR__ . '/../vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\IOFactory;

    $spreadsheet = new Spreadsheet();
    $sheetIndex = 0;
    foreach ($tables as $table) {
        $res = $mysqli->query("SELECT * FROM `" . $mysqli->real_escape_string($table) . "`");
        if (!$res) {
            echo "Warning: failed to query {$table}: " . $mysqli->error . "\n";
            continue;
        }
        $rows = array();
        while ($row = $res->fetch_assoc()) $rows[] = $row;

        if ($sheetIndex > 0) {
            $spreadsheet->createSheet();
        }
        $sheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
        $sheet->setTitle(substr($table,0,31));
        if (count($rows) > 0) {
            $sheet->fromArray(array_keys($rows[0]), NULL, 'A1');
            $sheet->fromArray($rows, NULL, 'A2');
        }
        $sheetIndex++;
    }
    $filename = $outdir . '/export_' . date('Y-m-d_His') . '.xlsx';
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save($filename);
    echo "Wrote workbook to {$filename}\n";

} else {
    foreach ($tables as $table) {
        $res = $mysqli->query("SELECT * FROM `" . $mysqli->real_escape_string($table) . "`");
        if (!$res) {
            echo "Warning: failed to query {$table}: " . $mysqli->error . "\n";
            continue;
        }
        $rows = array();
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }

        if ($format === 'csv') {
            $file = $outdir . '/' . $table . '_' . date('Y-m-d_His') . '.csv';
            $fh = fopen($file, 'w');
            if (count($rows) > 0) {
                fputcsv($fh, array_keys($rows[0]));
                foreach ($rows as $r) fputcsv($fh, $r);
            }
            fclose($fh);
            echo "Wrote {$table} (" . count($rows) . ") to {$file}\n";
        } else {
            $file = $outdir . '/' . $table . '_' . date('Y-m-d_His') . '.json';
            file_put_contents($file, json_encode($rows, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
            echo "Wrote {$table} (" . count($rows) . ") to {$file}\n";
        }
    }
}

$mysqli->close();
echo "Export complete.\n";
