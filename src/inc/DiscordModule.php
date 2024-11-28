<?php
/* Discord Oauth v.4.1
 * This file contains the core functions of the oauth2 script.
 * @author : MarkisDev
 * @copyright : https://markis.dev
 */

# Starting the script without session (no session required)
require_once $_SERVER['DOCUMENT_ROOT'] . "/src/define.php";
// Заменить $file_path на $configPath
$configPath = __config__;
if (file_exists($configPath)) {
    $yaml_data = read_yaml($configPath);
} else {
    throw new Exception("Configuration file not found: $configPath");
}

// Далее вы можете работать с $yaml_data...
$discord = $yaml_data['discord'];
$client_id = $discord['client_id'];
$secret_id = $discord['secret_id'];
$scopes = $discord['scopes'];
$redirect_url = $discord['redirect_url'];
$bot_token = $discord['bot'];
$guild_id = $discord['guild_id'];
$webhooks = $discord['webhooks'];

// Setting the base URL for API requests
$base_url = "https://discord.com";

// A function to generate a random string to be used as state | (protection against CSRF)
function gen_state()
{
    return bin2hex(openssl_random_pseudo_bytes(12));
}

// A function to generate oAuth2 URL for logging in
function url()
{
    global $client_id, $redirect_url, $scopes;
    $state = gen_state();
    // Ideally, store this state temporarily (e.g., in the database, or pass it via the URL)
    return 'https://discordapp.com/oauth2/authorize?response_type=code&client_id=' . $client_id . '&redirect_uri=' . $redirect_url . '&scope=' . urlencode($scopes) . "&state=" . $state;
}

// A function to initialize and return access token if successful
function init($code, $state, $stored_state)
{
    global $client_id, $secret_id, $redirect_url;
    
    // Verify state to prevent CSRF
    if (!check_state($state, $stored_state)) {
        return false;  // Invalid login due to mismatched state
    }

    $url = "https://discord.com/api/oauth2/token";
    $data = array(
        "client_id" => $client_id,
        "client_secret" => $secret_id,
        "grant_type" => "authorization_code",
        "code" => $code,
        "redirect_uri" => $redirect_url
    );
    
    // Exchange the code for an access token
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);

    if (isset($results['access_token'])) {
        return $results['access_token'];  // Return the access token if successful
    }
    return false;  // Token exchange failed
}

// A function to get user information | (identify scope)
function get_user($access_token)
{
    global $base_url;
    $url = $base_url . "/api/users/@me";
    $headers = array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $access_token);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);

    if ($results) {
        $user_data = [
            'username' => $results['username'],
            'discriminator' => $results['discriminator'],
            'user_id' => $results['id'],
            'user_avatar' => $results['avatar'],
            'email' => $results['email']
        ];
        
        // if ($email) {
        //     $user_data['email'] = $results['email'];
        // }

        return $user_data;
    }
    return null;  // Return null if no user data found
}

// A function to give roles to the user
function give_role($guildid, $roleid, $bot_token, $user_id)
{
    global $base_url;
    $data = json_encode(array("roles" => array($roleid)));
    $url = $base_url . "/api/guilds/$guildid/members/$user_id/roles/$roleid";
    $headers = array('Content-Type: application/json', 'Authorization: Bot ' . $bot_token);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);
    return $results;
}

// A function to get user guilds | (guilds scope)
function get_guilds($access_token)
{
    global $base_url;
    $url = $base_url . "/api/users/@me/guilds";
    $headers = array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $access_token);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);
    return $results;
}

// A function to verify if login is legit without using sessions
function check_state($state, $stored_state)
{
    // Compare the provided state with the stored one
    if ($state == $stored_state) {
        return true;
    }
    return false;
}
