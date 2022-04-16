<?php
session_start();

use Class\Auth;

$outgoing_id = $_SESSION['unique_id'] ?? '';

if (!empty($outgoing_id)) {
    // include_once dirname(__FILE__) . "./class/Auth.class.php";
    $_auth = new Auth;

    $arrayData = $_auth->getOtherUsers($outgoing_id);

    $output = "";

    if (count($arrayData) > 0) {
        include_once dirname(__FILE__) . './utils/Ui_usersChat.php';
    } else {
        $output .= "No users are available to chat";
    }
    echo $output;
}
