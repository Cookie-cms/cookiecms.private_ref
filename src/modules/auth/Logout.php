<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/mysql.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/yamlReader.php";
$file_path = $_SERVER['DOCUMENT_ROOT'] . '/configs/config.yml';
$yaml_data = read_yaml($file_path);

// Include JWT library
use \Firebase\JWT\JWT;

// Secret key for encoding the JWT (make sure this is kept secure)
define('JWT_SECRET_KEY', 'your_secret_key');

// Function to check if the token is in the blacklist
function is_token_blacklisted($conn, $jwt) {
    $stmt = $conn->prepare("SELECT * FROM token_blacklist WHERE token = :token");
    $stmt->bindParam(':token', $jwt);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to add the token to the blacklist
function blacklist_token($conn, $jwt) {
    $stmt = $conn->prepare("INSERT INTO token_blacklist (token) VALUES (:token)");
    $stmt->bindParam(':token', $jwt);
    $stmt->execute();
}

// Get the Authorization header from the request
$headers = getallheaders();
if (isset($headers['Authorization'])) {
    $jwt = str_replace("Bearer ", "", $headers['Authorization']); // Extract token from 'Bearer <token>'
} else {
    echo json_encode([
        'error' => true,
        'msg' => 'Authorization header not found'
    ]);
    exit();
}

try {
    // Decode and verify the JWT token
    $decoded = JWT::decode($jwt, JWT_SECRET_KEY, ['HS256']);

    // Check if the token is already blacklisted
    if (is_token_blacklisted($conn, $jwt)) {
        echo json_encode([
            'error' => true,
            'msg' => 'Token has already been blacklisted'
        ]);
        exit();
    }

    // Add token to the blacklist (invalidate it)
    blacklist_token($conn, $jwt);

    // Return success response
    echo json_encode([
        'error' => false,
        'msg' => 'Logout successful, token added to blacklist'
    ]);
} catch (Exception $e) {
    // Handle token errors (e.g., invalid token, expired token)
    echo json_encode([
        'error' => true,
        'msg' => 'Invalid or expired token: ' . $e->getMessage()
    ]);
    error_log("JWT Error: " . $e->getMessage(), 0);
}
?>
