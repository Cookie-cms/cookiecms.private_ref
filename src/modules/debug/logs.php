<?php
header('Content-Type: application/json; charset=utf-8');

$logFile = $_SERVER['DOCUMENT_ROOT'] . '/logs/app.log';
if (file_exists($logFile)) {
    echo json_encode([
        'success' => true,
        'logs' => file_get_contents($logFile)
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Log file not found'
    ]);
}
