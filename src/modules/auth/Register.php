<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once $_SERVER['DOCUMENT_ROOT'] . "/src/define.php";

$file_path = $_SERVER['DOCUMENT_ROOT'] . '/configs/config.yml';
$yaml_data = read_yaml($file_path);
require_once __mysql__;

// define('JWT_SECRET_KEY', $yaml_data['securecode']);


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
        return response("Password must be at least 8 characters", true, 400, null, null);
        exit();
    }

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE BINARY mail = :email");
        $stmt->bindParam(':email', $mail);
        $stmt->execute();
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            return response("Email is already registered.", true, 409, null, null);
            exit();
        }

        
        $userID = mt_rand(000000000000000000, 999999999999999999);

        // $userID = $id;
        // echo($userID);
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);



        $stmt = $conn->prepare("INSERT INTO users (id, mail, password) VALUES (:id, :mail, :password)");
        $stmt->bindParam(':id', $userID);
        $stmt->bindParam(':mail', $mail);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomCode = '';
        $length = 6;
        for ($i = 0; $i < $length; $i++) {
            $randomCode .= $characters[random_int(0, strlen($characters) - 1)];
        }
        $timexp = time() + 3600;
        // $timexp = 0;
        $action = 1;
        $stmt = $conn->prepare("INSERT INTO verify_codes (userid, code, expire, action) VALUES (:userid, :code, :expire, :action)");
        $stmt->bindParam(':userid', $userID);
        $stmt->bindParam(':code', $randomCode);
        $stmt->bindParam(':expire', $timexp);        
        $stmt->bindParam(':action', $action);        
        $stmt->execute();
        return response("Registration successful. Please proceed to login.", false, 200, "/home", null);
        exit(); // Ensure no further code is executed
    } catch (PDOException $e) {
        log_to_file("[ERROR] PDOException: " . $e->getMessage(), 0);

        return response("An error occurred during registration. Please try again later.", true, 400, null, null);
    }
} else {
    return response("Incomplete form data provided.", true, 400, null, null);

}
?>
