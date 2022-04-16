<?php
session_start();

use Class\Message;

$outgoing_id = $_SESSION['unique_id'] ?? '';

$incoming_id = $_POST['incoming_id'] ?? '';
$message = $_POST['message'] ?? '';

if (!empty($outgoing_id)  && !empty($incoming_id) && !empty($message)) {
    // require_once dirname(__FILE__) . './class/Message.class.php';
    $_mess = new Message;

    $res = $_mess->newMessage($outgoing_id, $incoming_id, $message);

    // $res['status'] === 'error'
} else {
    header("location: ../login.php");
}
