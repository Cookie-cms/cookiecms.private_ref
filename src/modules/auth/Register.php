<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/mysql.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/yamlReader.php";

$file_path = $_SERVER['DOCUMENT_ROOT'] . '/configs/config.yml';
$yaml_data = read_yaml($file_path);


define('JWT_SECRET_KEY', $yaml_data['securecode']);


function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

if (isset($data['mail']) && isset($data['password'])) {
    $mail = validate($data['mail']);
    $password = validate($data['password']);

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        return response(true, 400, null, "Invalid email format", null);
        exit();
    }

    if (strlen($password) < 8) {
        return response(true, 400, null, "Password must be at least 8 characters", null);
        exit();
    }

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE BINARY mail = :email");
        $stmt->bindParam(':email', $mail);
        $stmt->execute();
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            return response(true, 409,null, "Email is already registered.", null);
            exit();
        }

        $id = mt_rand(000000000000000000, 999999999999999999);

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO users (id, mail, password) VALUES (:id, :mail, :password)");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':mail', $mail);
        $stmt->bindParam(':password', $hashed_password);
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
        return response(false, 200, "/login", "Registration successful. Please proceed to login.", null);
        exit(); // Ensure no further code is executed
    } catch (PDOException $e) {
        error_log("PDOException: " . $e->getMessage(), 0);
        return response(true, 400, null, "An error occurred during registration. Please try again later.", null);
    }
} else {
    return response(true, 400, null, "Incomplete form data provided.", null);
}
?>
