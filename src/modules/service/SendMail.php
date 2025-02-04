<?php
# This file is part of CookieCms.
#
# CookieCms is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# CookieCms is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with CookieCms. If not, see <http://www.gnu.org/licenses/>.

function welcomemsg($email, $accountid, $date, $username) {
    require_once $_SERVER['DOCUMENT_ROOT'] . "/define.php";
    require __CM__ . "inc/mail.php";
    require  __CD__ . "pages/global.php";

    $templateFile = __CD__ . 'mailtemplates/welcome.html';
    
    $dateformat = date('Y-m-d H:i:s', $date);
    // echo $urlicon;
    $username = $username ? $username : 'User';
    $keys = [
        '[USER_NAME]' => $username,
        '[logoimg]' => $urlicon,
        '[ACCOUNT_ID]' => $accountid,
        '[CREATED_DATE]' => $dateformat,
    ];

    if (!file_exists($templateFile)) {
        throw new Exception("Template file not found");
    }
    
    $message = file_get_contents($templateFile);

    foreach ($keys as $key => $value) {
        $message = str_replace($key, $value, $message);
    }

    $mail->setFrom('noreply@coffeedev.dev', 'Noreply');
    
    $mail->addAddress($email);

    $mail->Subject = 'Welcome to our project';
    $mail->Body = $message; 
    $mail->isHTML(true);
    $mail->send();
}  

function verificationmsg($email, $username, $code, $userid) {
    require_once $_SERVER['DOCUMENT_ROOT'] . "/define.php";
    require __CM__ . "inc/mail.php";
    require  __CD__ . "pages/global.php";

    $templateFile = __CD__ . 'mailtemplates/emailVerification.html';
    if ($CFPyaml_data['basic']['ssl'] ==  true) {
        $link = "https://" . $CFPyaml_data['basic']['domain'] . "api/mail/?userid=$userid&code=$code";
    } else {
        $link = "http://" . $CFPyaml_data['basic']['domain'] . "api/mail/?userid=$userid&code=$code";
    }
    // echo $urlicon;
    $username = $username ? $username : 'User';
    $keys = [
        // '[USER_NAME]' => $username,
        // '[logoimg]' => $urlicon,
        // '[LINK]' => $link,
        '[YOUR_VERIFICATION_CODE]' => $code,
    ];

    if (!file_exists($templateFile)) {
        throw new Exception("Template file not found");
    }
    
    $message = file_get_contents($templateFile);

    foreach ($keys as $key => $value) {
        $message = str_replace($key, $value, $message);
    }

    $mail->setFrom('noreply@coffeedev.dev', 'Noreply');
    
    $mail->addAddress($email);

    $mail->Subject = 'Mail Verification';
    $mail->Body = $message; 
    $mail->isHTML(true);
    $mail->send();
} 