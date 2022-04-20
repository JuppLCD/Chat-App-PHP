<?php
require_once __DIR__ . './../vendor/autoload.php';

session_start();

use Php\class\Auth;
use Php\class\Message;

$outgoing_id = $_SESSION['unique_id'] ?? '';

if (!empty($outgoing_id)) {
    require_once __DIR__ . './class/Auth.class.php';
    $_auth = new Auth;

    $arrayData = $_auth->getOtherUsers($outgoing_id);

    $output = "";

    if (count($arrayData) > 0) {
        require_once __DIR__ . './class/Message.class.php';
        $_mess = new Message;

        $output = $_mess->getUsersChat($arrayData, $outgoing_id, $output);
    } else {
        $output .= "No users are available to chat";
    }
    echo $output;
}
