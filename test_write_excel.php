<?php
// Define minimal CI constants to allow including the library
if (!defined('BASEPATH')) define('BASEPATH', true);
if (!defined('APPPATH')) define('APPPATH', __DIR__ . '/application/');
require_once __DIR__ . '/application/libraries/Excel.php';
$e = new Excel();
$sheet = $e->create();
$sheet->getActiveSheet()->setCellValue('A1', 'hello');
$e->write('Xlsx', __DIR__ . '/test_output.xlsx');
echo "WROTE\n";
?>