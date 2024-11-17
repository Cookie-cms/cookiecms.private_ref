<?php
// error_reporting(E_ALL);
// ini_set('display_errors', true);
define('__RD__', $_SERVER['DOCUMENT_ROOT']);
define('__UD__', __RD__ . 'uploads/');
// define('__CM__', __RD__ . '/engine/modules/');
// define('__CML__', __RD__ . 'engine/modules/');
define('__CD__', __RD__ . '/engine/');
define('__CDL__', __RD__ . '/engine/');
define('__CF__', __CD__ . 'configs/');
define('__ven__', __RD__ . '/../vendor/autoload.php');
define('__hub__', __RD__ . '/../');
define('__CI__', __RD__ . '/inc/');
require_once __CI__ . "yamlReader.php";
// require_once __CI__ . "mail.php";
require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/yamlReader.php";


/**
 * Outputs a JSON-encoded error response.
 *
 * @param string $message - Error message to display.
 * @param string|null $details - Additional details (optional).
 */
function responseWithError(string $message, string $details = null): void {
    $errorResponse = ['status' => 'error', 'msg' => $message];
    if ($details) {
        $errorResponse['details'] = $details;
    }
    echo json_encode($errorResponse);
    exit;
}