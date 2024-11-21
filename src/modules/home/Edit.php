<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

// Include necessary files and libraries
require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/mysql.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/yamlReader.php";
$file_path = $_SERVER['DOCUMENT_ROOT'] . '/configs/config.yml';
$yaml_data = read_yaml($file_path);


$securecode = $yaml_data['securecode'];
$jwt = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');

$status = isJwtExpiredOrBlacklisted($jwt, $conn, $securecode);

$inputData = file_get_contents('php://input');

$data = json_decode($inputData, true);



// Извлечение данных из тела запроса
// $inputData = json_decode(file_get_contents("php://input"), true);
if (!$inputData) {
    response(400, "Bad Request: Invalid JSON");
    exit;
}

// Логика обработки запросов
try {
    if (isset($inputData['username'], $inputData['password'])) {
        // Обновление имени пользователя
        updateUsername($conn, $user['id'], $inputData['username'], $inputData['password']);
    } elseif (isset($inputData['password'], $inputData['new_password'])) {
        // Изменение пароля
        changePassword($conn, $user['id'], $inputData['password'], $inputData['new_password']);
    } elseif (isset($inputData['cape'])) {
        // Смена плаща
        changeCape($conn, $user['id'], $inputData['cape']);
    } elseif (isset($_FILES['skin'])) {
        // Загрузка скина
        uploadSkin($conn, $user['id'], $_FILES['skin']);
    } else {
        response(400, "Bad Request: Missing required fields");
        exit;
    }
} catch (Exception $e) {
    response(500, "Internal Server Error: " . $e->getMessage());
    exit;
}

// Функция для обновления имени пользователя
function updateUsername($conn, $userId, $newUsername, $currentPassword) {
    // Validate current password
    if (!validatePassword($conn, $userId, $currentPassword)) {
        response(401, "Invalid password");
        exit;
    }

    // Check if the new username is already taken by another user
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
    $stmt->execute([':username' => $newUsername, ':id' => $userId]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        response(400, "Username is already taken by another user");
        exit;
    }

    // Update the username if it's not taken
    $stmt = $conn->prepare("UPDATE users SET username = :username WHERE id = :id");
    $stmt->execute([':username' => $newUsername, ':id' => $userId]);

    response(200, "Username updated successfully");
}


// Функция для изменения пароля
function changePassword($conn, $userId, $currentPassword, $newPassword) {
    if (!validatePassword($conn, $userId, $currentPassword)) {
        response(401, "Invalid password");
        exit;
    }
    $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
    $stmt->execute([':password' => password_hash($newPassword, PASSWORD_DEFAULT), ':id' => $userId]);
    response(200, "Password updated successfully");
}

// Функция для смены плаща
function changeCape($conn, $userId, $capeId) {
    $stmt = $conn->prepare("UPDATE users SET cape_id = :cape WHERE id = :id");
    $stmt->execute([':cape' => $capeId, ':id' => $userId]);
    response(200, "Cape updated successfully");
}


function uploadSkin($conn, $userId, $skinFile) {
    global $yaml_data;

    // Directory where skins will be stored
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/skins/";

    // Ensure the directory exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Validate file upload
    if (!isset($skinFile) || $skinFile['error'] !== UPLOAD_ERR_OK) {
        response(400, "No file uploaded or upload error");
    }

    // Validate file type (only PNG images allowed)
    $fileType = mime_content_type($skinFile['tmp_name']);
    if ($fileType !== 'image/png') {
        response(400, "Only PNG images are allowed");
    }

    // Validate image dimensions (must be 64x64 pixels)
    list($width, $height) = getimagesize($skinFile['tmp_name']);
    if ($width !== 64 || $height !== 64) {
        response(400, "Image dimensions must be 64x64 pixels");
    }

    // Generate a unique file name
    $skin_name = generateUUIDv4();
    $newFileName = "$skin_name.png";
    $targetFile = $targetDir . $newFileName;

    // Count existing skins for the user
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_rows FROM skin_lib WHERE uid = :uid");
    $stmt->execute([':uid' => $userId]);
    $count_skin = $stmt->fetch(PDO::FETCH_ASSOC);
    $maxfile = (int)$yaml_data['MaxSavedSkins']; // Maximum skins allowed

    // Check if the user has reached the skin limit
    $used_skins = (int)$count_skin['total_rows'];
    if ($used_skins >= $maxfile) {
        response(400, "You have reached the limit of $maxfile skins.");
    }

    // Attempt to move the uploaded file
    if (move_uploaded_file($skinFile['tmp_name'], $targetFile)) {
        // Update the database with the new skin
        try {
            $stmt = $conn->prepare("INSERT INTO skin_lib (uid, name, nff) VALUES (:uid, :name, :nff)");
            $stmt->execute([
                ':uid' => $userId,
                ':name' => $newFileName,
                ':nff' => $skin_name
            ]);

            response(200, "Skin uploaded successfully", ['filename' => $newFileName]);
        } catch (Exception $e) {
            response(500, "Failed to save skin in the database: " . $e->getMessage());
        }
    } else {
        response(500, "Failed to upload skin");
    }
}


// Функция для загрузки скина
function removeSkin($conn, $userId, $skinId) {
    global $yaml_data;

    // Query to find the skin in the database
    $stmt = $conn->prepare("SELECT name FROM skin_lib WHERE uid = :uid AND id = :skinId");
    $stmt->execute([
        ':uid' => $userId,
        ':skinId' => $skinId
    ]);
    
    // Fetch the skin data
    $skinData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$skinData) {
        response(404, "Skin not found.");
        return;
    }

    // Get the skin file name
    $skinFileName = $skinData['name'];
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/skins/";
    $skinFilePath = $targetDir . $skinFileName;

    // Remove the skin entry from the database
    try {
        $deleteStmt = $conn->prepare("DELETE FROM skin_lib WHERE uid = :uid AND id = :skinId");
        $deleteStmt->execute([
            ':uid' => $userId,
            ':skinId' => $skinId
        ]);

        // Check if the skin file exists and delete it from the server
        if (file_exists($skinFilePath)) {
            unlink($skinFilePath);
        }

        response(200, "Skin removed successfully.");

    } catch (Exception $e) {
        response(500, "Failed to remove skin: " . $e->getMessage());
    }
}


// Вспомогательная функция проверки пароля
function validatePassword($conn, $userId, $password) {
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return password_verify($password, $user['password']);
}

// Вспомогательная функция для ответа
function response($statusCode, $message, $data = []) {
    http_response_code($statusCode);
    echo json_encode([
        'error' => $statusCode >= 400,
        'msg' => $message,
        'url' => null,
        'data' => $data
    ]);
    exit;
}

