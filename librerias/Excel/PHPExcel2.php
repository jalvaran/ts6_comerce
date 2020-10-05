<?php




error_reporting(E_ALL);
require __DIR__ . "/phpspreadsheet/vendor/autoload.php";
require_once __DIR__ . '/phpspreadsheet/src/Bootstrap.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Helper\Sample;

$helper = new Sample();

// Return to the caller script when runs by CLI
if ($helper->isCli()) {
    return;
}
