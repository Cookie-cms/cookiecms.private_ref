<?php
# This file is part of CookieCms.
#
# CookieCms is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# CookieCms is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with CookieCms. If not, see <http://www.gnu.org/licenses/>.
require_once $_SERVER['DOCUMENT_ROOT'] . "/src/define.php";


use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;



function isJwtExpiredOrBlacklisted($jwt, $pdo, $securecode) {
    // Проверка наличия JWT
    if (empty($jwt)) {
        log_message('JWT token is missing.');
        return response("Invalid token", false, 400);  
        // responseWithError('JWT токен отсутствует.');
    }

    // Проверка в черном списке
    $stmt = $pdo->prepare("SELECT expiration FROM blacklisted_jwts WHERE jwt = :jwt LIMIT 1");
    $stmt->execute([':jwt' => $jwt]);
    $blacklistEntry = $stmt->fetch(PDO::FETCH_ASSOC);

    // Если токен найден в черном списке
    if ($blacklistEntry) {
        log_message('JWT token is blacklisted.');
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
        log_message('Invalid token: ' . $e->getMessage());
        return response("Invalid token", false, 400);  
        // responseWithError('Недействительный токен.', ['error' => $e->getMessage()]);
        return;
    }
}

function generateUUIDv4() {
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
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


function response($message, $status = false, $statusCode = 200, $url = null, $data = null) {
    $response = [
        "error" => $status,
        "msg" => $message,
        "url" => $url,
        "data" => $data
    ];

    http_response_code($statusCode);
    echo json_encode($response);
    exit;
}

function log_message($message) {

    $date = date('Y-m-d H:i:s');
    $logMessage = "[$date] $message\n";

    // global $webSocketServer;

    $logFile = $_SERVER['DOCUMENT_ROOT'] . '/logs/app.log';
    
    // $webSocketServer->debug($logMessage);
    // Сохраняем сообщение в файл
    file_put_contents($logFile, $logMessage, FILE_APPEND);

    // Если есть клиенты WebSocket, отправляем им сообщение
    // if ($clients) {
    //     foreach ($clients as $client) {
    //         $client->send(json_encode([
    //             'type' => 'log',
    //             'message' => $logMessage,
    //         ]));
    //     }
    // }
}

