<?php
session_start();

use Class\Auth;
use Class\Respuestas;

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!empty($email) && !empty($password)) {
    include_once dirname(__FILE__) . "./class/Auth.class.php";
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
    include_once dirname(__FILE__) . "./class/Response.class.php";
    $_resClass = new Respuestas;

    header('Content-Type: application/json');
    $resData = $_resClass->err('', 400);
    echo json_encode($resData);
}
