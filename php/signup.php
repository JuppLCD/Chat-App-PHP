<?php
session_start();

use Class\Auth;
use Class\Respuestas;

$fname = $_POST['fname'] ?? '';
$lname = $_POST['lname'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!empty($fname) && !empty($lname) && !empty($email) && !empty($password) && isset($_FILES['image'])) {
    // require_once dirname(__FILE__) . "./class/Auth.class.php";
    $_auth = new Auth;

    $resData = $_auth->signup($fname, $lname, $email, $password, $_FILES['image']);

    if (isset($resData["result"]["error_id"])) {
        $responseCode = $resData["result"]["error_id"];
        http_response_code($responseCode);
    } else {
        $unique_id = $_auth->unique_id;
        $_SESSION['unique_id'] = $unique_id;
        http_response_code(200);
    }

    header('Content-Type: application/json');
    echo json_encode($resData);
} else {
    // include_once dirname(__FILE__) . "./class/Response.class.php";
    $_resClass = new Respuestas;

    header('Content-Type: application/json');
    $resData = $_resClass->err('Todos los campos son obligatorios', 400);
    echo json_encode($resData);
}
