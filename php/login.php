<?php
require_once dirname(__FILE__) . './../vendor/autoload.php';
session_start();

// No funciona el autoload al querer hacer login
require_once __DIR__ . './class/Auth.class.php';
require_once __DIR__ . './class/Response.class.php';

use Php\class\Auth;
use Php\class\Respuestas;

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!empty($email) && !empty($password)) {
    $_auth = new Auth;

    $resData = $_auth->login($email, $password);

    if (isset($resData["result"]["error_id"])) {
        $responseCode = $resData["result"]["error_id"];
        http_response_code($responseCode);
    } else {
        $_SESSION['unique_id'] = $_auth->unique_id;
        http_response_code(200);
    }
    header('Content-Type: application/json');
    echo json_encode($resData);
} else {
    $_resClass = new Respuestas;

    header('Content-Type: application/json');
    $resData = $_resClass->err('', 400);
    echo json_encode($resData);
}
