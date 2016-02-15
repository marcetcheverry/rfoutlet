<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Please edit this file with your outlet codes and pulse length
require_once('configuration.inc.php');

if (!file_exists($codeSendPath)) {
    error_log("$codeSendPath is missing, please edit the script", 0);
    die(json_encode(array('success' => false)));
}

$outletLight = $_POST['outletId'];
$outletStatus = $_POST['outletStatus'];

if (is_empty($outletLight) || is_empty($outletStatus)) {
    error_log('Missing POST paremeters', 0);
    die(json_encode(array('success' => false)));
}

if ($outletLight == "6") {
    // 6 is all 5 outlets combined
    if (function_exists('array_column')) {
        // PHP >= 5.5
        $codesToToggle = array_column($codes, $outletStatus);
    } else {
        $codesToToggle = array();
        foreach ($codes as $outletCodes) {
            array_push($codesToToggle, $outletCodes[$outletStatus]);
        }
    }
} else {
    // One
    $codesToToggle = array($codes[$outletLight][$outletStatus]);
}

foreach ($codesToToggle as $codeSendCode) {
    shell_exec($codeSendPath . ' ' . $codeSendCode . ' -p ' . $codeSendPIN . ' -l ' . $codeSendPulseLength);
    sleep(1);
}

die(json_encode(array('success' => true)));
