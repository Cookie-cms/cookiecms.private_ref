<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/src/define.php"; // Define your variables like client_id, secret_id, etc.
require_once $_SERVER['DOCUMENT_ROOT'] . "/src/inc/DiscordModule.php";

// Define the URL array with the link key
$url = array("link" => url()); // Or you can use the shorthand: $url = ["link" => url()];

return response(null, false, 200, null, $url);
