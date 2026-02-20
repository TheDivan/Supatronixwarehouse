<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

// PhpSpreadsheet wrapper for legacy Excel usage
// Usage (legacy): $this->load->library('excel'); $this->excel->load($file);
// This wrapper exposes minimal helpers and falls back to direct PhpSpreadsheet usage.
if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    throw new RuntimeException('Composer autoload not found; please run "composer install".');
}
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Excel
{
    protected $spreadsheet;

    public function __construct()
    {
        // nothing by default
    }

    // Create a new spreadsheet instance
    public function create()
    {
        $this->spreadsheet = new Spreadsheet();
        return $this->spreadsheet;
    }

    // Load an existing file and return Spreadsheet instance
    public function load($filename)
    {
        $this->spreadsheet = IOFactory::load($filename);
        return $this->spreadsheet;
    }

    // Return the current spreadsheet instance
    public function get()
    {
        return $this->spreadsheet;
    }

    // Create writer and save to filename or php://output
    // $writerType examples: 'Xlsx', 'Xls', 'Csv', 'Html'
    public function write($writerType = 'Xlsx', $target = 'php://output', $spreadsheet = null)
    {
        $sheet = $spreadsheet ?? $this->spreadsheet;
        if (! $sheet instanceof Spreadsheet) {
            throw new InvalidArgumentException('No Spreadsheet instance available to write.');
        }

        $writer = IOFactory::createWriter($sheet, $writerType);
        return $writer->save($target);
    }

    // Convenience: output to browser with headers
    public function output($filename = 'export.xlsx', $writerType = 'Xlsx', $spreadsheet = null)
    {
        $sheet = $spreadsheet ?? $this->spreadsheet;
        if (! $sheet instanceof Spreadsheet) {
            throw new InvalidArgumentException('No Spreadsheet instance available to write.');
        }

        // content-type mapping
        $ct = [
            'Xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Xls' => 'application/vnd.ms-excel',
            'Csv' => 'text/csv',
            'Html' => 'text/html',
        ];

        $type = isset($ct[$writerType]) ? $ct[$writerType] : 'application/octet-stream';
        header('Content-Type: ' . $type);
        header('Content-Disposition: attachment;filename="' . basename($filename) . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($sheet, $writerType);
        $writer->save('php://output');
    }
}
