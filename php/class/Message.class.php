<?php
include_once dirname(__FILE__) . "./Response.class.php";
require_once(dirname(__FILE__) . './../db/Conection.class.php');


class Message extends Conexion
{
    private $outgoing_id = '';
    private $incoming_id = '';
    private $message = '';

    public function newMessage($outgoing_id, $incoming_id, $message)
    {
        $_resClass = new Respuestas;

        $this->validCharactersOfMessage($outgoing_id, $incoming_id, $message, $_resClass);

        if ($_resClass->response['status'] !== 'ok') {
            return $_resClass->response;
        }

        $this->creteMessage($_resClass);

        if ($_resClass->response['status'] !== 'ok') {
            return $_resClass->response;
        }

        return $_resClass->response = [
            'status' => "ok",
            "result" => "success"
        ];
    }

    private function validCharactersOfMessage($outgoing_id, $incoming_id, $message, $_resClass)
    {
        $outgoing_id = parent::validCharacters($outgoing_id);
        $incoming_id = parent::validCharacters($incoming_id);
        $message = parent::validCharacters($message);

        if (empty($outgoing_id) || empty($incoming_id) || empty($message)) {
            return $_resClass->err("Data not valid", 200);
        }
        $this->outgoing_id = $outgoing_id;
        $this->incoming_id = $incoming_id;
        $this->message = $message;
    }

    private function creteMessage($_resClass)
    {

        $query = "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg) VALUES ('{$this->incoming_id}', '{$this->outgoing_id}', '{$this->message}')";

        $idMessage = parent::nonQueryId($query);

        if ($idMessage === 0) {
            return $_resClass->err("Something went wrong. Please try again!", 500);
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
