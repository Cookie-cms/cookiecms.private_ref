<?php
error_reporting(E_ALL);
ini_set('display_errors', true);


require_once __mysql__;
// require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/yamlReader.php";
$file_path = $_SERVER['DOCUMENT_ROOT'] . '/configs/config.yml';


$yaml_data = read_yaml($file_path);

// Include JWT library
use \Firebase\JWT\JWT;

// Secret key for encoding the JWT (make sure this is kept secure)
// define('JWT_SECRET_KEY', $yaml_data['securecode']);

// Get the raw POST data
$inputData = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($inputData, true);

// Log the incoming request body for debugging
// error_log(print_r($data, true)); // Logs the raw POST data

function RegisterDiscord($user) {
    global $conn, $yaml_data;

    $mail = $user['email'];
    $userid = $user['user_id']; 
    $stmt = $conn->prepare("SELECT * FROM users WHERE BINARY mail = :email");
    $stmt->bindParam(':email', $mail);
    $stmt->execute();
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);


    $id = mt_rand(000000000000000000, 999999999999999999);

    $stmt = $conn->prepare("INSERT INTO users (id, mail) VALUES (:id, :mail)");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':mail', $mail);
    $stmt->execute();

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomCode = '';
    $length = 6;
    for ($i = 0; $i < $length; $i++) {
        $randomCode .= $characters[random_int(0, strlen($characters) - 1)];
    }
    $time = 0;

    $stmt = $conn->prepare("INSERT INTO verify_codes (userid, code, expire) VALUES (:userid, :code, :expire)");
    $stmt->bindParam(':userid', $id);
    $stmt->bindParam(':code', $randomCode);
    $stmt->bindParam(':expire', $time);        
    $stmt->execute();

    $urlAvatar = "https://cdn.discordapp.com/avatars/" . $userid . "/" . "size=128";

    
    return response("Registration successful. Please proceed to login.", false, 200, "/home", null);

}