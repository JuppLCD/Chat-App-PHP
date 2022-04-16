<?php
session_start();

$incoming_id = $_POST['incoming_id'] ?? '';

$outgoing_id = $_SESSION['unique_id'] ?? '';

if (!empty($outgoing_id) && !empty($incoming_id)) {
    include_once dirname(__FILE__) . './class/Message.class.php';
    $_mess = new Message;

    $output = $_mess->getChat($outgoing_id, $incoming_id);

    echo $output;
} else {
    header("location: ../login.php");
}
