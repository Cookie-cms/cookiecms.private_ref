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

// Include necessary files and libraries
require_once __mysql__;
require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/yamlReader.php";
$file_path = $_SERVER['DOCUMENT_ROOT'] . '/configs/config.yml';
$yaml_data = read_yaml($file_path);

// Include JWT library
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key; // Import Key class for specifying algorithm

// Secret key for encoding the JWT (make sure this is kept secure)
define('JWT_SECRET_KEY', $yaml_data['securecode']);

// Function to check if the token is in the blacklist
function is_token_blacklisted($conn, $jwt) {
    $stmt = $conn->prepare("SELECT * FROM blacklisted_jwts WHERE jwt = :token");
    $stmt->bindParam(':token', $jwt);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to add the token to the blacklist
function blacklist_token($conn, $jwt) {
    $stmt = $conn->prepare("INSERT INTO blacklisted_jwts (jwt) VALUES (:token)");
    $stmt->bindParam(':token', $jwt);
    return $stmt->execute(); // Return success status
}

// Get the Authorization header from the request
$headers = getallheaders();
if (isset($headers['Authorization'])) {
    $jwt = str_replace("Bearer ", "", $headers['Authorization']); // Extract token from 'Bearer <token>'
} else {
    return response("Authorization header not found", true, 400, null, null);
    exit();
}

try {
    // Decode and verify the JWT token
    // Use Key class for specifying algorithm
    $decoded = JWT::decode($jwt, new Key(JWT_SECRET_KEY, 'HS256'));
    
    // Check if the token is already blacklisted
    if (is_token_blacklisted($conn, $jwt)) {
        return response("Token has already been blacklisted", true, 400, "/login", null);
        exit();
    }

    // Add token to the blacklist (invalidate it)
    blacklist_token($conn, $jwt);


    return response("Logout successful, token added to blacklist", false, 200, $homeUrl, $jwt);
} catch (Exception $e) {
    
    log_to_file("[ERROR] JWT Error: " . $e->getMessage(), 0);
    return response("Invalid or expired token", true, 400, "/login", null);

}
