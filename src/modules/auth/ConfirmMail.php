<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/mysql.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/yamlReader.php";
$file_path = $_SERVER['DOCUMENT_ROOT'] . '/configs/config.yml';


$yaml_data = read_yaml($file_path);

define('JWT_SECRET_KEY', $yaml_data['securecode']);


// Get the raw POST data
$inputData = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($inputData, true);
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'status' => 'error',
        'message' => 'Unsupported request method.'
    ]);
    return;
};
// Log the incoming request body for debugging
// error_log(print_r($data, true)); // Logs the raw POST data
$stmt = $conn->prepare("SELECT * FROM verify_codes WHERE code = :code");
$stmt->bindParam(':code', $data['code']);
$stmt->execute();
$code = $stmt->fetch(PDO::FETCH_ASSOC);
// Example response
// var_dump($code);
// echo($user);
if ($code) {
    $stmt = $conn->prepare("UPDATE users SET mail_verify = 1 WHERE id = :id");
    $stmt->bindParam(':id', $code['userid']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("DELETE FROM verify_codes WHERE code = :code;");
    $stmt->bindParam(':code', $data['code']);
    $stmt->execute();

    echo json_encode([
        'error' => false,
        'msg' => 'Email confirmed successfully',
        'url' => '/login',
        'data'=> (object)[]
    ]);
} else {
    echo json_encode([
        'error' => true,
        'msg' => 'Invalid or expired token',
        'url'=> null,
        'data'=> (object)[]
    ]);
}
