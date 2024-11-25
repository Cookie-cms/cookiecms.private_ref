<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/define.php";
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;



function isJwtExpiredOrBlacklisted($jwt, $pdo, $securecode) {
    // Проверка наличия JWT
    if (empty($jwt)) {
        // responseWithError('JWT токен отсутствует.');
    }

    // Проверка в черном списке
    $stmt = $pdo->prepare("SELECT expiration FROM blacklisted_jwts WHERE jwt = :jwt LIMIT 1");
    $stmt->execute([':jwt' => $jwt]);
    $blacklistEntry = $stmt->fetch(PDO::FETCH_ASSOC);

    // Если токен найден в черном списке
    if ($blacklistEntry) {
        if (time() > $blacklistEntry['expiration']) {
            return true; // Токен истек и находится в черном списке
        }
        return true; // Токен в черном списке, но еще не истек
    }

    // Проверка на истечение срока действия
    try {
        $decoded = JWT::decode($jwt, new Key($securecode, 'HS256'));
        $exp = $decoded->exp ?? 0;

        if ($exp < time()) {
            return true; // Токен истек
        }

        // Если токен валиден, вернуть данные
        return [
            'status' => 'success',
            'data' => $decoded
        ];
    } catch (Exception $e) {
        // responseWithError('Недействительный токен.', ['error' => $e->getMessage()]);
        return;
    }
}

function generateUUIDv4() {
    $data = random_bytes(16);

    // Set version to 0100 (UUID version 4)
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    // Set variant to 10xx
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

    return sprintf(
        '%08s-%04s-%04s-%04s-%12s',
        bin2hex(substr($data, 0, 4)),
        bin2hex(substr($data, 4, 2)),
        bin2hex(substr($data, 6, 2)),
        bin2hex(substr($data, 8, 2)),
        bin2hex(substr($data, 10, 6))
    );
}


function response($status=false,$statusCode=200, $url=null,  $message, $data = null) {
    // Set the correct header
    header('Content-Type: application/json');

    // Prepare the response array
    $response = [
        "error" => ($status),  // If status code >= 400, set error as true
        "msg" => $message,
        "url" => $url,
        "data" => $data
    ];

    // Set the HTTP status code
    http_response_code($statusCode);

    // Output the response as JSON
    echo json_encode($response);
    exit;
}
