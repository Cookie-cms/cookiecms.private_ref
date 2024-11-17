<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
header('Content-Type: application/json; charset=utf-8');

require $_SERVER['DOCUMENT_ROOT'] . "/define.php";

$configPath = $_SERVER['DOCUMENT_ROOT'] . '/configs/routes.yml';

try {
    // Load the routes from the YAML config
    $routes = read_yaml($configPath);

    // Get and sanitize the current request URI
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $requestUri = filter_var($requestUri, FILTER_SANITIZE_URL);

    if (empty($routes[$requestUri])) {
        responseWithError("Route not defined");
    } else {
        $modulePath = $_SERVER['DOCUMENT_ROOT'] . '/modules/' . $routes[$requestUri];

        // Check if the corresponding file exists and include it
        if (file_exists($modulePath)) {
            include $modulePath;
        } else {
            responseWithError("Module file not found");
        }
    }
} catch (Exception $e) {
    responseWithError("An error occurred", $e->getMessage());
}

?>
