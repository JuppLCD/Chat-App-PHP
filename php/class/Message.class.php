<?php

namespace Php\class;

require_once __DIR__ . './../model/MessageModel.class.php';
require_once __DIR__ . './Response.class.php';

use Php\model\MessageModel;
use Php\class\Respuestas;

class Message
{
    public function newMessage($outgoing_id, $incoming_id, $message)
    {
        $_resClass = new Respuestas;
        $_messageModel = new MessageModel;

        if (empty($outgoing_id) || empty($incoming_id) || empty($message)) {
            return $_resClass->err("Data not valid", 400);
        }

        $data = [
            'outgoing_id' => $outgoing_id,
            'incoming_id' => $incoming_id,
            'message' => $message
        ];
        $result = $_messageModel->create($data);

        if (isset($result['error'])) {
            if ($result['error'] === 'Bad Data') {
                return $_resClass->err('Data not valid', 400);
            } else {
                return $_resClass->err('Error to create message', 500);
            }
        }

        return $_resClass->response = [
            'status' => "ok",
            "result" => "success"
        ];
    }

    public function getChat($outgoing_id, $incoming_id)
    {
        $_messageModel = new MessageModel;
        $output = '';

        $data = [
            'outgoing_id' => $outgoing_id,
            'incoming_id' => $incoming_id,
        ];

        $arrayData = $_messageModel->getAll($data);

        if (isset($arrayData['error'])) {
            //! Ver como hacer con posible error al buscar en DB
            return;
        }

        if (count($arrayData) === 0) {
            $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
        } else {
            foreach ($arrayData as $userData) {
                if ($userData['incoming_msg_id'] === $data['incoming_id']) {
                    $output .= '<div class="chat outgoing">
                                <div class="details">
                                    <p>' . $userData['msg'] . '</p>
                                </div>
                            </div>';
                } else {
                    $output .= '<div class="chat incoming">
                                <img src="./../../php/images/' . $userData['img'] . '" alt="">
                                <div class="details">
                                    <p>' . $userData['msg'] . '</p>
                                </div>
                            </div>';
                }
            }
        }

        return  $output;
    }

    public function getUsersChat($arrayData, $outgoing_id, $output)
    {
        foreach ($arrayData as $userData) {

            $_messageModel = new MessageModel;

            $data = [
                'outgoing_id' => $outgoing_id,
                'unique_id' => $userData['unique_id'],
            ];

            $messageFromUser = $_messageModel->getLastMessages($data);

            if (isset($messageFromUser['error'])) {
                //! Ver como hacer con posible error al buscar en DB
                return;
            }

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

            $output .= '<a href="./chat.php?user_id=' . $userData['unique_id'] . '">
                    <div class="content">
                        <img src="./../../php/images/' . $userData['img'] . '" alt="' . $userData['fname'] . '_' . $userData['lname'] . '-' . $userData['unique_id'] . '">
                            <div class="details">
                                <span>' . $userData['fname'] . " " . $userData['lname'] . '</span>
                                <p>' . $you . $msg . '</p>
                            </div>
                    </div>
                    <div class="status-dot ' . $offline . '"><i class="fas fa-circle"></i></div>
                </a>';
        };
        return $output;
    }
}
