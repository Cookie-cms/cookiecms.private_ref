<?php
/* Discord Oauth v.4.1
 * This file contains the core functions of the oauth2 script.
 * @author : MarkisDev
 * @copyright : https://markis.dev
 */

# Starting session so we can store all the variables
require_once $_SERVER['DOCUMENT_ROOT'] . "/define.php";

$file_path = $_SERVER['DOCUMENT_ROOT'] . '/configs/config.yml';
$yaml_data = read_yaml($file_path);  // Assuming read_yaml is a valid function
$discord = $yaml_data['discord'];

// Access specific values
$client_id = $discord['client_id'];
$secret_id = $discord['secret_id'];
$scopes = $discord['scopes'];
$redirect_url = $discord['redirect_url'];
$bot_token = $discord['bot'];
$guild_id = $discord['guild_id'];
$webhooks = $discord['webhooks'];

# Setting the base url for API requests
$base_url = "https://discord.com";

# A function to generate a random string to be used as state | (protection against CSRF)
function gen_state()
{
    $_SESSION['state'] = bin2hex(openssl_random_pseudo_bytes(12));
    return $_SESSION['state'];
}

# A function to generate oAuth2 URL for logging in
function url()
{
    global $client_id, $redirect_url, $scopes;
    $state = gen_state();
    return 'https://discordapp.com/oauth2/authorize?response_type=code&client_id=' . $client_id . '&redirect_uri=' . $redirect_url . '&scope=' . urlencode($scopes) . "&state=" . $state;
}

# A function to initialize and store access token in SESSION to be used for other requests
function init($redirect_url, $client_id, $client_secret, $bot_token = null)
{
    if ($bot_token != null) {
        $GLOBALS['bot_token'] = $bot_token;
    }
    $code = $_GET['code'];
    $state = $_GET['state'];
    
    # Check if $state == $_SESSION['state'] to verify if the login is legit
    if (!check_state($state)) {
        return false;  // State doesn't match, invalid login
    }
    
    $url = $GLOBALS['base_url'] . "/api/oauth2/token";
    $data = array(
        "client_id" => $client_id,
        "client_secret" => $client_secret,
        "grant_type" => "authorization_code",
        "code" => $code,
        "redirect_uri" => $redirect_url
    );
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);
    
    if (isset($results['access_token'])) {
        $_SESSION['access_token'] = $results['access_token'];
        return true;  // Successfully initialized
    }
    return false;  // Error in response
}

# A function to get user information | (identify scope)
function get_user($email = null)
{
    global $base_url;
    $url = $base_url . "/api/users/@me";
    $headers = array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $_SESSION['access_token']);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);
    $_SESSION['user'] = $results;
    $_SESSION['username'] = $results['username'];
    $_SESSION['discrim'] = $results['discriminator'];
    $_SESSION['user_id'] = $results['id'];
    $_SESSION['user_avatar'] = $results['avatar'];
    
    if ($email) {
        $_SESSION['email'] = $results['email'];
    }
}

# A function to give roles to the user
function give_role($guildid, $roleid)
{
    global $bot_token, $base_url;
    $data = json_encode(array("roles" => array($roleid)));
    $url = $base_url . "/api/guilds/$guildid/members/" . $_SESSION['user_id'] . "/roles/$roleid";
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

# A function to get user guilds | (guilds scope)
function get_guilds()
{
    global $base_url;
    $url = $base_url . "/api/users/@me/guilds";
    $headers = array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $_SESSION['access_token']);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);
    return $results;
}

# A function to fetch information on a single guild | (requires bot token)
function get_guild($id)
{
    global $bot_token, $base_url;
    $url = $base_url . "/api/guilds/$id";
    $headers = array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Bot ' . $bot_token);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);
    return $results;
}

# A function to get user connections | (connections scope)
function get_connections()
{
    global $base_url;
    $url = $base_url . "/api/users/@me/connections";
    $headers = array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $_SESSION['access_token']);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);
    return $results;
}

# Function to make user join a guild | (guilds.join scope)
function join_guild($guildid)
{
    global $bot_token, $base_url;
    $data = json_encode(array("access_token" => $_SESSION['access_token']));
    $url = $base_url . "/api/guilds/$guildid/members/" . $_SESSION['user_id'];
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

# A function to verify if login is legit
function check_state($state)
{
    if ($state == $_SESSION['state']) {
        return true;
    }
    return false;
}
