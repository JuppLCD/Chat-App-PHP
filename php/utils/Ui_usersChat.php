<?php
require_once dirname(__FILE__) . './class/Message.class.php';
$_mess = new Message;

foreach ($arrayData as $userData) {

    $messageFromUser = $_mess->getLastMessages($userData, $outgoing_id);

    $result = 'No message available';
    $you = '';
    $msg = '';

    // Ver si hubo conversacion con algun otro usuario
    if (isset($messageFromUser[0])) {
        $result = $messageFromUser[0]['msg'];
        $msg = (strlen($result) > 28) ? substr($result, 0, 28) . '...' : $result;
        //Quien fue el utimo en enviar mensaje
        $you = ($outgoing_id == $messageFromUser[0]['outgoing_msg_id']) ? "You: " : "";
    }

    $offline = ($userData['status'] == "Offline now") ? 'offline' : '';
    $hid_me = ($outgoing_id == $userData['unique_id']) ? "hide" : "";

    $output .= '<a href="./chat.php?user_id=' . $userData['unique_id'] . '">
                    <div class="content">
                        <img src="./../../php/images/' . $userData['img'] . '" alt="">
                            <div class="details">
                                <span>' . $userData['fname'] . " " . $userData['lname'] . '</span>
                                <p>' . $you . $msg . '</p>
                            </div>
                    </div>
                    <div class="status-dot ' . $offline . '"><i class="fas fa-circle"></i></div>
                </a>';
};
