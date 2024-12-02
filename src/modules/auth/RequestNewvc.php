<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once $_SERVER['DOCUMENT_ROOT'] . "/src/define.php";

$file_path = $_SERVER['DOCUMENT_ROOT'] . '/configs/config.yml';
$yaml_data = read_yaml($file_path);
require_once __mysql__;

function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

if (isset($data['mail'])) {
    $mail = validate($data['mail']);

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        return response(true, 400, null, "Invalid email format", null);
        exit();
    }

    try {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE BINARY mail = :email");
        $stmt->bindParam(':email', $mail);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return response("Email not found.", true, 404, null, null);
            exit();
        }

        // Generate a new verification code
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomCode = '';
        $length = 6;
        for ($i = 0; $i < $length; $i++) {
            $randomCode .= $characters[random_int(0, strlen($characters) - 1)];
        }

        $timexp = time() + 3600; // Expires in 1 hour
        $action = 1;

        // Insert the new verification code
        $stmt = $conn->prepare("INSERT INTO verify_codes (userid, code, expire, action) VALUES (:userid, :code, :expire, :action)");
        $stmt->bindParam(':userid', $user['id']);
        $stmt->bindParam(':code', $randomCode);
        $stmt->bindParam(':expire', $timexp);
        $stmt->bindParam(':action', $action);
        $stmt->execute();

        return response("New verification code generated successfully.", false, 200, null, null);
    } catch (PDOException $e) {
        error_log("PDOException: " . $e->getMessage(), 0);
        log_to_file("[ERROR] PDOException: " . $e->getMessage(), 0);

        return response("An error occurred while generating the verification code. Please try again.", true, 400, null, null);
    }
} else {
    return response("Email not provided.", true, 400, null, null);
}
?>
