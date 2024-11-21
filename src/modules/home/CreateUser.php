<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
require_once $_SERVER['DOCUMENT_ROOT'] . "/define.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/mysql.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/yamlReader.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/tools.php";
$file_path = $_SERVER['DOCUMENT_ROOT'] . '/configs/config.yml';


$yaml_data = read_yaml($file_path);

// Include JWT library
use \Firebase\JWT\JWT;

// Secret key for encoding the JWT (make sure this is kept secure)
define('JWT_SECRET_KEY', $yaml_data['securecode']);

// Get the raw POST data
$inputData = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($inputData, true);

// Log the incoming request body for debugging
// error_log(print_r($data, true)); // Logs the raw POST data

$securecode = $yaml_data['securecode'];
$jwt = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');

$status = isJwtExpiredOrBlacklisted($jwt, $conn, $securecode);
// var_dump($status);
// $status = $status['data'];

if ($status) {
    $userId = $status['data']->sub;

    $stmt = $conn->prepare("SELECT username, uuid, mail_verify FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($user['username']) || !empty($user['uuid'])) {
        // If username or UUID already exists
        responseWithError("You already have a Player account", $data);
        return;
    }

    $stmt = $conn->prepare("SELECT username FROM users WHERE username = :username");
    $stmt->bindParam(':username', $data['username']);
    $stmt->execute();
    $username = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($username) {
        echo "Username already taken.";
    } else {
        // Generate UUID and update user
        $uuid = generateUUIDv4();
        $stmt = $conn->prepare("UPDATE users SET uuid = :uuid, username = :username WHERE id = :id");

        $stmt->bindParam(':id', $userId); // Use correct user ID
        $stmt->bindParam(':uuid', $uuid);
        $stmt->bindParam(':username', $data['username']);

        // Debug: Output the bound parameters
        // var_dump([
        //     'id' => $userId,
        //     'uuid' => $uuid,
        //     'username' => $data['username'],
        // ]);

        $stmt->execute();
        $affectedRows = $stmt->rowCount();

        if ($affectedRows > 0) {
            echo "User updated successfully. Rows affected: $affectedRows.";
        } else {
            echo "Update executed, but no rows were affected. Rows affected: $affectedRows.";
        }
    }

    // Construct response
    $response = [
        "error" => false,
        "msg" => 200,
        "url" => null,
        "data" => []
    ];

    // Return response as JSON
    echo json_encode($response);
}
