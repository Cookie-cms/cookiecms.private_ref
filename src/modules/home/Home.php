<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
require_once $_SERVER['DOCUMENT_ROOT'] . "/src/define.php";

require_once __mysql__;
// require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/yamlReader.php";
// require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/tools.php";
$file_path = __config__;

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

// Check if JWT is valid
$status = isJwtExpiredOrBlacklisted($jwt, $conn, $securecode);

if ($status) {
    // Fetch the user by ID using the sub value from JWT
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $status['data']->sub);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Ensure that user data is available
    if (empty($user['username']) || empty($user['uuid']) || empty($user['password'])) {
        // Handle the case where the user doesn't exist
        
        $response = ["data" => [
            "username_create" => false,
            "password_create" => false
        ]];

        if (empty($user['username']) || empty($user['uuid'])){
            $response = ["data" => [
                "username_create" => empty($user['username'])
            ]];
        }
        if (empty($user['password'])){
            $response = ["data" => [
                "password_create" => empty($user['password'])
            ]];
        }

        return response("Your account is not finished", true, 401,"/login",$response);
        // responseWithError("Pls create user", $data);
        return;
    }

    $uuid = $user['uuid'];

    $sql = "SELECT cloaks.*, cloaks_lib.name AS cloak_name
    FROM cloaks
    JOIN cloaks_lib ON cloaks.cid = cloaks_lib.id
    WHERE cloaks.uid = :id;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $status['data']->sub);
    $stmt->execute();
    $capes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    function getUserSkins($conn, $userId) {
        // Query to get the skins for the specific user
        $stmt = $conn->prepare("SELECT id, name, nff FROM skins_lib WHERE uid = :uid");
        $stmt->execute([':uid' => $userId]);
        
        // Fetch all the skins for the user
        $skins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $skins;
    }

    
    
    // var_dump($capes);
// echo($status['data']->sub);
    // Format the capes into a response array
    $capeList = [];
    $skinList = getUserSkins($conn, $status['data']->sub);
    foreach ($capes as $cape) {
        // Add to cape list with proper casting
        $capeList[] = [
            "Id" => isset($cape['cid']) ? (int)$cape['cid'] : null, // Handle null safety
            "Name" => isset($cape['cloak_name']) ? $cape['cloak_name'] : ""   // Default to empty string if not set
        ];
    }

    

    // Prepare the final response
    $response = [
        "error" => false,
        "msg" => "",
        "url" => null,
        "data" => [
            "Username" => $user['username'],
            "Uuid" => $user['uuid'],
            "Selected_Cape" => 0,
            "Selected_Skin" => 0,
            "Capes" => $capeList,
            "Skin" => $skinList,
            "Discord_integration" => NULL,
            "Discord" => [
                "Discord_Global_Name" => "",
                "Discord_Ava" => ""
            ],
            "Mail_verification" => $user['mail_verify']
        ]
        ];

    // Return the response as JSON
    echo json_encode($response);
} else {
    // If JWT is invalid or blacklisted
    $data = [
        "code" => 401,
        "msg" => "Invalid JWT"
    ];
    return response("Invalid JWT", true, 401,"/login",$data);
    // responseWithError("Invalid JWT", $data);
}
