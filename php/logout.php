<?php
session_start();

use Class\Auth;

$logout_id = $_GET['logout_id'] ?? '';

if (isset($_SESSION['unique_id']) && !empty($logout_id)) {
    // require_once dirname(__FILE__) . "./class/Auth.class.php";
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
