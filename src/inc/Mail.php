<?php
// error_reporting(E_ALL);
// ini_set('display_errors', true);
require_once $_SERVER['DOCUMENT_ROOT'] . "/define.php";

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$file_path = $_SERVER['DOCUMENT_ROOT'] . '/configs/config.yml';
$yaml_data = read_yaml($file_path);

// echo($file_path);
// Access the 'database' section
$databaseConfig = $yaml_data['smtp'];
// var_dump($databaseConfig);
// var_dump($databaseConfig);
// Access specific values
$host = $databaseConfig['host'];
$SMTPAuth = $databaseConfig['SMTPAuth'];
$Username = $databaseConfig['Username'];
$Password = $databaseConfig['Password'];
$SMTPSecure = $databaseConfig['SMTPSecure'];
$Port = $databaseConfig['Port'];
//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = $host;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = $SMTPAuth;                                   //Enable SMTP authentication
    $mail->Username   = $Username;                     //SMTP username
    $mail->Password   = $Password;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = $Port;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}