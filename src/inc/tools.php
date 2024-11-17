<?php

function isJwtExpiredOrBlacklisted($jwt, $pdo, $securecode ) {
    // Check if the JWT is blacklisted
    $stmt = $pdo->prepare("SELECT expiration FROM blacklisted_jwts WHERE jwt = :jwt LIMIT 1");
    $stmt->execute([':jwt' => $jwt]);
    $blacklistEntry = $stmt->fetch(PDO::FETCH_ASSOC);

    // If it's found in the blacklist, check expiration
    if ($blacklistEntry) {
        // If current time is greater than the expiration time, the token is expired and blacklisted
        if (time() > $blacklistEntry['expiration']) {
            return true; // Token is expired and blacklisted
        }
        return true; // Token is still blacklisted but not expired yet
    }

    // Check if the JWT token is expired based on its own expiration claim
    // Decode the JWT token (use a JWT library like Firebase JWT)
    try {
        $decoded = \Firebase\JWT\JWT::decode($jwt, $securecode, 'HS256');
        $exp = $decoded->exp;

        // If the token has expired, return true
        if ($exp < time()) {
            return true;
        }

        return false; // Token is valid and not expired
    } catch (Exception $e) {
        return true; // Token is invalid
    }
}
