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
define('JWT_SECRET_KEY', $yaml_data['securecode']);

// Get the raw POST data
$inputData = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($inputData, true);

// Log the incoming request body for debugging
// error_log(print_r($data, true)); // Logs the raw POST data

// Check if the JSON contains 'username' and 'password'
if (isset($data['username']) && isset($data['password'])) {

    function validate($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Validate and assign variables
    $username = validate($data['username']);
    $password = validate($data['password']);

    // Function to check if input is a valid email
    function is_email($input) {
        return filter_var($input, FILTER_VALIDATE_EMAIL);
    }

    try {
        if (is_email($username)) {
            // If it's an email, query the email field
            $stmt = $conn->prepare("SELECT * FROM users WHERE BINARY mail = :email");
            $stmt->bindParam(':email', $username);
        } else {
            // Otherwise, tфreat it as a username
            $stmt = $conn->prepare("SELECT * FROM users WHERE BINARY username = :username");
            $stmt->bindParam(':username', $username);
        }

        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // var_dump($user);

        if (!$user){
            echo json_encode([
                'error' => true,
                'msg' => 'Incorrect username or password'
            ]);
            return;
        }
        if ($user['mail_verify'] == 0) {
            // Code to send a verification email
            // sendVerificationEmail($user['email']);
            echo json_encode([
                'error' => true,
                'msg' => 'Pls verify your Mail.',
                'url' => null,
                'data' => []
            ]);
            return;
        }
        

        // Check if user exists and the password is correct
        if ($user && password_verify($password, $user['password'])) {
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
                echo json_encode([
                    'error' => true,
                    'msg' => 'JWT error: ' . $e->getMessage()
                ]);
                error_log("JWT Error: " . $e->getMessage(), 0);
                exit();
            }

            // Prepare the URL for the redirect
            $homeUrl = "/home"; // This is the URL that the user will be redirected to

            // Send response with structured format
            echo json_encode([
                'error' => false,
                'msg' => 'Login successful',
                'url' => $homeUrl,
                'data' => [
                    'jwt' => $jwt  // The JWT token for authenticated requests
                ]
            ]);
        } else {
            echo json_encode([
                'error' => true,
                'msg' => 'Incorrect username or password'
            ]);
            return;
        }
    } catch(PDOException $e) {
        // Output error information
        echo json_encode([
            'error' => true,
            'msg' => 'Database error: ' . $e->getMessage()
        ]);
        error_log("Database Error: " . $e->getMessage(), 0);
        return;

    }

} else {
    // Handle missing username or password in JSON
    echo json_encode([
        'error' => true,
        'msg' => 'Username or password not provided'
    ]);
    return;

}
