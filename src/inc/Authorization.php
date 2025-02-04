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

use \Firebase\JWT\JWT;

function verifyAuthorization($pdo, $securecode)
{
    // Get the headers
    $headers = getallheaders();

    // Ensure the Authorization header is present
    if (!isset($headers['Authorization'])) {
        http_response_code(401); // Unauthorized
        echo json_encode(["error" => "Authorization header is missing."]);
        exit;
    }

    // Extract the JWT token
    if (!preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
        http_response_code(401); // Unauthorized
        echo json_encode(["error" => "Authorization header is not in the correct format."]);
        exit;
    }
    $jwt = $matches[1];

    // Check if the token is expired or blacklisted
    if (isJwtExpiredOrBlacklisted($jwt, $pdo, $securecode)) {
        http_response_code(403); // Forbidden
        echo json_encode(["error" => "Token is expired or blacklisted."]);
        exit;
    }

    // Decode the JWT
    try {
        $decoded = JWT::decode($jwt, $securecode);
    } catch (Exception $e) {
        http_response_code(403); // Forbidden
        echo json_encode(["error" => "Invalid token."]);
        exit;
    }

    // If everything is fine, return the decoded JWT data
    return $decoded;
}
