<?php

namespace Php\class;

require_once __DIR__ . './../db/Conection.class.php';
require_once __DIR__ . './Response.class.php';

use Php\db\Conexion;
use Php\class\Respuestas;

class Message extends Conexion
{
    private $outgoing_id = '';
    private $incoming_id = '';
    private $message = '';

    private Respuestas $_resClass;

    public function newMessage($outgoing_id, $incoming_id, $message)
    {
        $this->_resClass = new Respuestas;

        $this->validCharactersOfMessage($outgoing_id, $incoming_id, $message);

        if ($this->_resClass->response['status'] !== 'ok') {
            return $this->_resClass->response;
        }

        $this->creteMessage();

        if ($this->_resClass->response['status'] !== 'ok') {
            return $this->_resClass->response;
        }

        return $this->_resClass->response = [
            'status' => "ok",
            "result" => "success"
        ];
    }

    private function validCharactersOfMessage($outgoing_id, $incoming_id, $message)
    {
        $outgoing_id = parent::validCharacters($outgoing_id);
        $incoming_id = parent::validCharacters($incoming_id);
        $message = parent::validCharacters($message);

        if (empty($outgoing_id) || empty($incoming_id) || empty($message)) {
            return $this->_resClass->err("Data not valid", 200);
        }
        $this->outgoing_id = $outgoing_id;
        $this->incoming_id = $incoming_id;
        $this->message = $message;
    }

    private function creteMessage()
    {

        $query = "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg) VALUES ('{$this->incoming_id}', '{$this->outgoing_id}', '{$this->message}')";

        $idMessage = parent::nonQueryId($query);

        if ($idMessage === 0) {
            return $this->_resClass->err("Something went wrong. Please try again!", 500);
        }
    }

    public function getChat($outgoing_id, $incoming_id)
    {
        $output = '';

        $arrayData = $this->getMessages($outgoing_id, $incoming_id);

        if (count($arrayData) === 0) {
            $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
        } else {
            foreach ($arrayData as $userData) {
                if ($userData['incoming_msg_id'] === $this->incoming_id) {
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

            $messageFromUser = $this->getLastMessages($userData, $outgoing_id);

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

    private function getMessages($outgoing_id, $incoming_id)
    {
        $this->outgoing_id = parent::validCharacters($outgoing_id);
        $this->incoming_id = parent::validCharacters($incoming_id);

        $sql = "SELECT * FROM messages LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
                WHERE (outgoing_msg_id = '{$this->outgoing_id}' AND incoming_msg_id = '{$this->incoming_id}') OR (outgoing_msg_id = '{$this->incoming_id}' AND incoming_msg_id = '{$this->outgoing_id}') ORDER BY msg_id";

        $arrayData = parent::getData($sql);
        return $arrayData;
    }

    public function getLastMessages($userData, $outgoing_id)
    {
        $queryMessagesUser = "SELECT * FROM messages WHERE (incoming_msg_id = '{$userData['unique_id']}' OR outgoing_msg_id = '{$userData['unique_id']}') AND (outgoing_msg_id = '{$outgoing_id}' OR incoming_msg_id = '{$outgoing_id}') ORDER BY msg_id DESC LIMIT 1";

        $messageFromUser = parent::getData($queryMessagesUser);

        return $messageFromUser;
    }
}
