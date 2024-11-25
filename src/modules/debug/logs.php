<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");  // You can replace '*' with specific domain if needed
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// If it's a pre-flight request, return immediately
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}


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
