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

$inputData = file_get_contents('php://input');

$data = json_decode($inputData, true);

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
    $timexp = time() + 3600;
    $action = 1;
    $stmt = $conn->prepare("INSERT INTO verify_codes (userid, code, expire, action) VALUES (:userid, :code, :expire, :action)");
    $stmt->bindParam(':userid', $id);
    $stmt->bindParam(':code', $randomCode);
    $stmt->bindParam(':expire', $timexp);        
    $stmt->bindParam(':action', $action);        
    $stmt->execute();
    // var_dump($user);
    // $urlAvatar = "https://cdn.discordapp.com/avatars/" . $userid . "/" . $user['user_avatar']. "?size=128";

    
    return response("Registration successful. Please proceed to registration.", false, 200, "/home", null);

}


