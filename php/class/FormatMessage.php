<?php

namespace Php\class;

class FormatMessage
{
    public function getAllChats($outgoing_id, $userData)
    {
        $result = 'No message available';
        $you = '';
        $msg = 'No messages...';

        // Ver si hubo conversacion con algun otro usuario
        if (isset($messageFromUser[0])) {
            $result = $messageFromUser[0]['msg'];
            $msg = (strlen($result) > 28) ? substr($result, 0, 28) . '...' : $result;
            //Quien fue el utimo en enviar mensaje
            $you = ($outgoing_id == $messageFromUser[0]['outgoing_msg_id']) ? "You: " : "";
        }

        $offline = ($userData['status'] == "Offline now") ? 'offline' : '';
        // $hid_me = ($outgoing_id == $userData['unique_id']) ? "hide" : "";

        $chat =  '<a href="./chat.php?user_id=' . $userData['unique_id'] . '">
                    <div class="content">
                        <img src="./../../php/images/' . $userData['img'] . '" alt="' . $userData['fname'] . '_' . $userData['lname'] . '-' . $userData['unique_id'] . '">
                            <div class="details">
                                <span>' . $userData['fname'] . " " . $userData['lname'] . '</span>
                                <p>' . $you . $msg . '</p>
                            </div>
                    </div>
                    <div class="status-dot ' . $offline . '"><i class="fas fa-circle"></i></div>
                </a>';
        return $chat;
    }

    public function getChat($output, $arrayData, $data)
    {
        if (count($arrayData) === 0) {
            $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
        } else {
            foreach ($arrayData as $userData) {
                if ($userData['incoming_msg_id'] === $data['incoming_id']) {
                    $output .= $this->sendMessage($userData['msg']);
                } else {
                    $output .= $this->incomingMessage($userData, $userData['msg']);
                }
            }
        }
        return $output;
    }

    public function incomingMessage(array $userData, string $message)
    {
        $incomingMSG = '<div class="chat incoming">
                                <img src="./../../php/images/' . $userData['img'] . '" alt="' . $userData['fname'] . '_' . $userData['lname'] . '-' . $userData['unique_id'] . '">
                                <div class="details">
                                    <p>' . $message . '</p>
                                </div>
                            </div>';
        return $incomingMSG;
    }

    public function sendMessage(string $message)
    {
        $sendMessage = '<div class="chat outgoing">
                                    <div class="details">
                                        <p>' . $message . '</p>
                                    </div>
                                </div>';
        return $sendMessage;
    }
}
