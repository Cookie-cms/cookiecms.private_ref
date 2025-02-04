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
require_once $_SERVER['DOCUMENT_ROOT'] . "/src/inc/yamlReader.php";
$file_path = __config__;
$yaml_data = read_yaml($file_path);
use \Firebase\JWT\JWT;

define('JWT_SECRET_KEY', $yaml_data['securecode']);

$inputData = file_get_contents('php://input');

$data = json_decode($inputData, true);


if (isset($data['username']) && isset($data['password'])) {

    function validate($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $username = validate($data['username']);
    $password = validate($data['password']);

    function is_email($input) {
        return filter_var($input, FILTER_VALIDATE_EMAIL);
    }

    try {
        if (is_email($username)) {
            $stmt = $conn->prepare("SELECT * FROM users WHERE BINARY mail = :email");
            $stmt->bindParam(':email', $username);
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE BINARY username = :username");
            $stmt->bindParam(':username', $username);
        }

        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // var_dump($user);

        if (!$user){
            return response("Incorrect username or password", true, 403);
        }
        if ($user['mail_verify'] == 0) {
            return response("Please verify your mail", true, 403);
        }
        

        if ($user && password_verify($password, $user['password'])) {
            $NameSite = $yaml_data['NameSite'];
            $payload = [
                'iss' => $NameSite, // Issuer of the token
                'sub' => $user['id'], // Store user ID in the token
                'iat' => time(), // Issued at
                'exp' => time() + 3600, // Expiry time (1 hour)
            ];

            try {
                $jwt = JWT::encode($payload, JWT_SECRET_KEY, 'HS256'); // Add 'HS256' as the algorithm
            } catch (Exception $e) {
                error_log("JWT Error: " . $e->getMessage(), 0);
                response("JWT Error",true,403);
                exit();
            }

            $homeUrl = "/home"; // This is the URL that the user will be redirected to

            $data = [
                    'jwt' => $jwt // The JWT token for authenticated requests
            ];
            response("Login successful", false, 200, $homeUrl, $data);
            } else {

            response('Incorrect username or password', true, 400, null, null);
            return ;
        }
    } catch(PDOException $e) {
        log_message("[ERROR] PDOException: " . $e->getMessage(), 0);
        response('Database Error', true, 400, null, null);
        return;

    }

} else {

    response('Username or password not provided', true, 400, null, null);
    return;

}
