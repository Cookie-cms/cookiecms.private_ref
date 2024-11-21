<?php
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
