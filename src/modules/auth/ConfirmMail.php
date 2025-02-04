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
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once __mysql__;
$file_path = __config__;

$yaml_data = read_yaml($file_path);

define('JWT_SECRET_KEY', $yaml_data['securecode']);

$inputData = file_get_contents('php://input');

$data = json_decode($inputData, true);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'status' => 'error',
        'message' => 'Unsupported request method.'
    ]);
    return;
}

$stmt = $conn->prepare("
    SELECT vc.userid, vc.action, vc.expire
    FROM verify_codes vc 
    JOIN users u ON vc.userid = u.id 
    WHERE vc.code = :code
");
$stmt->bindParam(':code', $data['code']);
$stmt->execute();
$code = $stmt->fetch(PDO::FETCH_ASSOC);

if ($code) {
    $time = time();
    if ($time > $code['expire']) {
        response('Token has expired', true, 400, null, null);
        return;
    }

    if ($code['action'] == 1) {
        $stmt = $conn->prepare("UPDATE users SET mail_verify = 1 WHERE id = :id");
        $stmt->bindParam(':id', $code['userid']);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM verify_codes WHERE code = :code");
        $stmt->bindParam(':code', $data['code']);
        $stmt->execute();

        response('Email confirmed successfully', false, 200, '/login', null);
    } else {
        response('Invalid or expired token', true, 400, null, null);
    }
} else {
    response('Invalid or expired token', true, 400, null, null);
}

?>
