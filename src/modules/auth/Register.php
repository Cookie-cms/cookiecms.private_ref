<?php
// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', true);

// Include necessary files
require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/mysql.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/yamlReader.php";

$file_path = $_SERVER['DOCUMENT_ROOT'] . '/configs/config.yml';
$yaml_data = read_yaml($file_path);

// Include JWT library
use \Firebase\JWT\JWT;

// Define secret key for JWT
define('JWT_SECRET_KEY', $yaml_data['securecode']);

// Helper function to sanitize input
function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Get raw POST data
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

// Log the request for debugging
error_log(print_r($data, true));

// Check if email and password are provided
if (isset($data['mail']) && isset($data['password'])) {
    $mail = validate($data['mail']);
    $password = validate($data['password']);

    // Check if the email is valid
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'error' => true,
            'msg' => 'Invalid email format'
        ]);
        exit();
    }

    // Check if the password meets the criteria (e.g., min 8 characters)
    if (strlen($password) < 8) {
        echo json_encode([
            'error' => true,
            'msg' => 'Password must be at least 8 characters'
        ]);
        exit();
    }

    try {
        // Check if the email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE BINARY mail = :email");
        $stmt->bindParam(':email', $mail);
        $stmt->execute();
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            echo json_encode([
                'error' => true,
                'msg' => 'Email is already registered'
            ]);
            exit();
        }

        // Generate a unique user ID
        $id = mt_rand(000000000000000000, 999999999999999999);

        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert the user into the database
        $stmt = $conn->prepare("INSERT INTO users (id, mail, password) VALUES (:id, :mail, :password)");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':mail', $mail);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();

        $NameSite = $yaml_data['NameSite'];
        $payload = [
            'iss' => $NameSite, // Issuer of the token
            'iat' => time(), // Issued at
            'exp' => time() + 14400, // Expiry time (1 hour)
            'id' => $id, // Store user ID in the token
        ];
        $jwt = JWT::encode($payload, JWT_SECRET_KEY, 'HS256'); // Add 'HS256' as the algorithm
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomCode = '';
        $length = 6;
        for ($i = 0; $i < $length; $i++) {
            $randomCode .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $randomCode;
        return json_encode([
            'error' => false,
            'msg' => 'Registration successful. Please proceed to registration.',
            'url' => '/register/step2',
            'data' => [
                    'jwt' => $jwt  // The JWT token for authenticated requests
            ]

        ]);
    } catch (PDOException $e) {
        // Log the error and return a user-friendly message
        error_log("PDOException: " . $e->getMessage(), 0);
        echo json_encode([
            'error' => true,
            'msg' => 'An error occurred during registration. Please try again later.'
        ]);
    }
} else {
    // Handle incomplete form submissions
    echo json_encode([
        'error' => true,
        'msg' => 'Incomplete form data provided.'
    ]);
}
?>
