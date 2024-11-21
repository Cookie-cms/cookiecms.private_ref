<?php
// Include necessary files
require_once $_SERVER['DOCUMENT_ROOT'] . "/define.php"; // Define your variables like client_id, secret_id, etc.
require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/mysql.php"; // Assuming this connects to your database
require_once $_SERVER['DOCUMENT_ROOT'] . "/modules/auth/LoginDiscord.php"; // Assuming this connects to your database
// require_once $_SERVER['DOCUMENT_ROOT'] . "/auth/RegisterDiscord.php"; // Assuming this connects to your database

// Check if user is coming from Discord's redirect after OAuth2 authentication
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $state = gen_state();

    // Initialize the token (assuming init() returns a token)
    $token = init($code, $state, $state);

    // Get user data from Discord API
    $user = get_user($token); // Fetch user details from Discord API

    // Assuming $user contains the discord ID and email
    $discord_id = $user['user_id']; // Discord ID
    $email = $user['email'];   // Email
    // echo("$discord_id   $email");
    // Prepare the database query to check both discord ID and email
    $stmt = $conn->prepare("SELECT * FROM users WHERE BINARY dsid = :discord_id AND BINARY mail = :email");
    $stmt->bindParam(':discord_id', $discord_id);
    $stmt->bindParam(':email', $email);

    // Execute the query
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // If the user exists with the matching Discord ID and email
        return LoginDiscord($result['mail']);
    } else {
        responseWithError("Not realised");
    }
}
?>