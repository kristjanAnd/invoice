<?php

header('Location: http://' . $_SERVER['SERVER_NAME']);

use BitWeb\IdServices\Authentication\IdCard\Authentication;

// make everything relative to the project root
chdir(dirname(dirname(__DIR__)));

// Autoload classes
include 'init_autoloader.php';

session_start();

$redirectUrl = urldecode($_GET["redirectUrl"]);

// remove these two lines if in production, these are for testing only
$_SERVER['SSL_CLIENT_S_DN'] = 'GN=Mari-Liis/SN=Männik/serialNumber=51001091072/C=EST';
$_SERVER[Authentication::SSL_CLIENT_VERIFY] = Authentication::SSL_CLIENT_VERIFY_SUCCESSFUL;

if (!Authentication::isSuccessful()) {
    $redirectUrl = '/user/login';
} else {
    Authentication::login();
}
$headerString = 'Location: ' . $redirectUrl;

header($headerString);
