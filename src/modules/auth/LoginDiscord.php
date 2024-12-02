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
define('JWT_SECRET_KEY', $yaml_data['securecode']);

// Get the raw POST data
$inputData = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($inputData, true);

// Log the incoming request body for debugging
// error_log(print_r($data, true)); // Logs the raw POST data

function LoginDiscord($mail) {
    global $conn, $yaml_data;
    $stmt = $conn->prepare("SELECT * FROM users WHERE BINARY mail = :email");
    $stmt->bindParam(':email', $mail);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user['mail_verify'] == 0) {
        // Code to send a verification email
        // sendVerificationEmail($user['email']);

        return response("Please verify your mail", true, 400, null, null);
    }
       // Generate the JWT token
       $NameSite = $yaml_data['NameSite'];
       $payload = [
           'iss' => $NameSite, // Issuer of the token
           'sub' => $user['id'], // Store user ID in the token
           'iat' => time(), // Issued at
           'exp' => time() + 3600, // Expiry time (1 hour)
       ];

       // Encode the JWT token, passing the algorithm
       try {
           $jwt = JWT::encode($payload, JWT_SECRET_KEY, 'HS256'); // Add 'HS256' as the algorithm
       } catch (Exception $e) {
           error_log("PDOException: " . $e->getMessage(), 0);
           return response("JWT Error", true, 400, null, null);
           exit();
       }

       // Prepare the URL for the redirect
       $homeUrl = "/home"; // This is the URL that the user will be redirected to

       // Send response with structured format
       $jwt = [
        'jwt' => $jwt  // The JWT token for authenticated requests
        ];
       
        return response("Login successful", false, 200, $homeUrl, $jwt);


}