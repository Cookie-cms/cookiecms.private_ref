<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

error_reporting(E_ALL);
ini_set('display_errors', true);
header('Content-Type: application/json; charset=utf-8');

// Define log levels: 0 (none), 1 (standard), 2 (full)
define('LOG_LEVEL', 2); // Change to 0, 1, or 2 as needed
$logFile = $_SERVER['DOCUMENT_ROOT'] . '/logs/app.log';

// Log function based on level
function log_message($message, $level = 1) {
    global $logFile;

    if (LOG_LEVEL >= $level) {
        $date = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$date] $message\n", FILE_APPEND);
    }
}
// Log detailed request information (used for level 2)
// Log detailed request information (used for level 2)
function log_request_details() {
    if (LOG_LEVEL < 2) return;

    $method = $_SERVER['REQUEST_METHOD']; // HTTP Method (e.g., GET, POST)
    $uri = $_SERVER['REQUEST_URI'];      // Full requested URI
    $headers = getallheaders();
    $body = file_get_contents('php://input');

    // Censor emails in the headers
    if (isset($headers['Authorization'])) {
        $headers['Authorization'] = preg_replace('/(Bearer\s)(\S+@\S+)/', '$1****@****.com', $headers['Authorization']);
    }

    // Censor email in the body if it's JSON
    if ($body && is_json($body)) {
        $decodedBody = json_decode($body, true);
        if (isset($decodedBody['mail'])) {
            $decodedBody['mail'] = preg_replace('/(.+)@(.+)/', '****@****.com', $decodedBody['mail']);
        }
        if (isset($decodedBody['password'])) {
            $decodedBody['password'] = '********';  // Mask the password
        }
        $body = json_encode($decodedBody);
    }

    log_message("Request: $method $uri", 2);
    log_message("Request Headers: " . json_encode($headers), 2);
    log_message("Request Body: " . $body, 2);
}

// Helper function to check if the body is JSON
function is_json($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}



// Skip logging for /debug/ requests
$isDebugRequest = strpos($_SERVER['REQUEST_URI'], '/debug/') !== false;

if (!$isDebugRequest) {
    log_message("===== New Request =====", 1);
    log_request_details();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/src/define.php";
$configPath = __config__; // Two levels up
$routesPath = $_SERVER['DOCUMENT_ROOT'] . '/configs/routes.yml'; // Two levels up
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/define.php'; // Two levels up

try {
    if (!$isDebugRequest) {
        log_message("Routes loaded successfully.", 1);
    }

    // Load the routes from the YAML config
    $routes = read_yaml($routesPath);

    // Get and sanitize the current request URI
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $requestUri = filter_var($requestUri, FILTER_SANITIZE_URL);
    
    if (!$isDebugRequest) {
        log_message("Sanitized Request URI: $requestUri", 1);
    }

    if (empty($routes[$requestUri])) {
        if (!$isDebugRequest) {
            log_message("Route not defined for: $requestUri", 1);
        }
        response(true, 404, null, "Route not defined", null);
    } else {
        $modulePath = $_SERVER['DOCUMENT_ROOT'] . '/src/modules/' . $routes[$requestUri];
        if (!$isDebugRequest) {
            log_message("Module path: $modulePath", 1);
        }

        // Check if the corresponding file exists and include it
        if (file_exists($modulePath)) {
            if (!$isDebugRequest) {
                log_message("Module file found. Including: $modulePath", 1);
            }
            include $modulePath;
        } else {
            if (!$isDebugRequest) {
                log_message("Module file not found: $modulePath", 1);
            }
            response(true, 400, null, "Module file not found", null);
        }
    }
} catch (Exception $e) {
    $msg = "An error occurred: " . $e->getMessage();
    if (!$isDebugRequest) {
        log_message($msg, 1);
    }
    response(true, 400, null, $msg, null);
    // response(true, 400, $msg, null, null);    
}

if (!$isDebugRequest) {
    log_message("Request finished.", 1);
}
?>
