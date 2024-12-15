<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
require_once $_SERVER['DOCUMENT_ROOT'] . "/src/define.php";
require_once __mysql__;

$file_path = $_SERVER['DOCUMENT_ROOT'] . '/configs/config.yml';
$yaml_data = read_yaml($file_path);

// Include JWT library
define('JWT_SECRET_KEY', $yaml_data['securecode']);

$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

// Log the incoming request body for debugging
// error_log(print_r($data, true));

if (empty($data['username'])) {
    return response("Username is required.", false, 400);
}

if (!empty($data['password']) && strlen($data['password']) < 6) {
    return response("Password must be at least 6 characters.", false, 400);
}

$securecode = $yaml_data['securecode'];
$jwt = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');

$status = isJwtExpiredOrBlacklisted($jwt, $conn, $securecode);

if ($status) {
    $userId = $status['data']->sub;

    $stmt = $conn->prepare("SELECT username, uuid, mail_verify, password FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($user['username']) || !empty($user['uuid']) || !empty($user['password'])) {
        return response("You already have a Player account", false, 409, "/home");
    }

    $stmt = $conn->prepare("SELECT username FROM users WHERE username = :username");
    $stmt->bindParam(':username', $data['username']);
    $stmt->execute();
    $username = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($username) {
        return response("Username already taken.", true, 409);
    } else {
        $uuid = generateUUIDv4();
        $stmt = $conn->prepare("UPDATE users SET uuid = :uuid, username = :username WHERE id = :id");

        $stmt->bindParam(':id', $userId);
        $stmt->bindParam(':uuid', $uuid);
        $stmt->bindParam(':username', $data['username']);
        $stmt->execute();

        $affectedRows = $stmt->rowCount();
        if ($affectedRows > 0) {
            echo "User updated successfully. Rows affected: $affectedRows.";
        } else {
            echo "Update executed, but no rows were affected. Rows affected: $affectedRows.";
        }
    }

    if (!empty($data['password'])) {
        // Hash the password
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        // Update the user's password
        $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        $affectedRows = $stmt->rowCount();
        if ($affectedRows > 0) {
            echo "Password updated successfully. Rows affected: $affectedRows.";
        } else {
            echo "Password update executed, but no rows were affected. Rows affected: $affectedRows.";
        }
    }

    return response("Created", true, 200, "/home");
}

return response("Invalid token or session expired", false, 401);
