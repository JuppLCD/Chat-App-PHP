<?php
session_start();
require_once __DIR__ . './../vendor/autoload.php';


use Php\class\Auth;

$logout_id = $_GET['logout_id'] ?? '';

if (isset($_SESSION['unique_id']) && !empty($logout_id)) {
    require_once __DIR__ . './class/Auth.class.php';

    $_auth = new Auth;

    $resData = $_auth->logout($logout_id);

    if (isset($resData["result"]["error_id"])) {
        header("location: ../src/view/users.php");
    } else {
        session_unset();
        session_destroy();
        header("location: ../src/view/login.php");
    }
} else {
    header("location: ../src/view/login.php");
}
