<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
require_once $_SERVER['DOCUMENT_ROOT'] . "/src/define.php";

require_once __mysql__;
// require_once $_SERVER['DOCUMENT_ROOT'] . "/src/inc/yamlReader.php";
// require_once $_SERVER['DOCUMENT_ROOT'] . "/src/inc/tools.php";
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

// print($jwt);
$status = isJwtExpiredOrBlacklisted($jwt, $conn, $securecode);
// var_dump($status);
// $status = $status['data'];

if ($status) {
    $userId = $status['data']->sub;

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($user['password'])) {
        // If username or UUID already exists
        response("Password is not set.", false, 400, "/home");
        return;
    }

    $stmt = $conn->prepare("SELECT username FROM users WHERE username = :username");
    $stmt->bindParam(':username', $data['username']);
    $stmt->execute();
    $username = $stmt->fetch(PDO::FETCH_ASSOC);

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
    return response("created", true, 200, "/home");

}

