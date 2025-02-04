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
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *"); // Replace '*' with specific domain if needed
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once $_SERVER['DOCUMENT_ROOT'] . "/src/define.php"; // Ensure this path is correct

// Define file path for configuration
$file_path = $_SERVER['DOCUMENT_ROOT'] . "/configs/config.yml"; // Adjust path if needed


// Check if it's a preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Read the YAML configuration
    $yaml_data = read_yaml($file_path);

    // Validate token
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $token = str_replace('Bearer ', '', $authHeader);

    if (empty($yaml_data['debugToken']) || $token !== $yaml_data['debugToken']) {
        http_response_code(403); // Forbidden
        echo json_encode([
            'error' => 403,
            'message' => 'Access denied. Invalid or missing token.',
        ]);
        exit;
    }

    // Debug log handling
    $logFile = $_SERVER['DOCUMENT_ROOT'] . '/logs/app.log';

    if (file_exists($logFile)) {
        echo json_encode([
            'success' => true,
            'logs' => file_get_contents($logFile)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Log file not found'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500); // Internal server error
    echo json_encode([
        'error' => 500,
        'message' => $e->getMessage(),
    ]);
}
?>
