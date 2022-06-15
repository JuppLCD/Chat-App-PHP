<?php

namespace Php\model;

require_once __DIR__ . './../db/Conection.class.php';

use Php\db\Conexion;

class MessageModel extends Conexion
{
    public function create(array $data)
    {
        // $data =>>> incoming_id, outgoing_id, message
        $arrayValidData = parent::validCharactersArray($data);

        if (isset($arrayValidData['outgoing_id']) || isset($arrayValidData['incoming_id']) || isset($arrayValidData['message'])) {
            return ['error' => 'Bad Data'];
        }

        $query = "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg) VALUES ('{$arrayValidData['incoming_id']}', '{$arrayValidData['outgoing_id']}', '{$arrayValidData['message']}')";

        $idMessage = parent::nonQueryId($query);

        if ($idMessage === 0) {
            return ['error' => 'Error to create message'];
        }
        return  [
            'ok' => true,
            'id' => $idMessage
        ];
    }

    public function getAll(array $data)
    {
        // $data =>>> incoming_id, outgoing_id
        $arrayValidData = parent::validCharactersArray($data);

        if (isset($arrayValidData['outgoing_id']) || isset($arrayValidData['incoming_id'])) {
            return ['error' => 'Bad Data'];
        }

        $sql = "SELECT * FROM messages LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
                WHERE (outgoing_msg_id = '{$arrayValidData['outgoing_id']}' AND incoming_msg_id = '{$arrayValidData['incoming_id']}') OR (outgoing_msg_id = '{$arrayValidData['incoming_id']}' AND incoming_msg_id = '{$arrayValidData['outgoing_id']}') ORDER BY msg_id";

        $arrayData = parent::getData($sql);
        return $arrayData;
    }

    public function getLastMessages($data)
    {
        // $data =>>> $userData['unique_id'], outgoing_id
        $arrayValidData = parent::validCharactersArray($data);

        if (isset($arrayValidData['outgoing_id']) || isset($arrayValidData['unique_id'])) {
            return ['error' => 'Bad Data'];
        }

        $queryMessagesUser = "SELECT * FROM messages WHERE (incoming_msg_id = '{$arrayValidData['unique_id']}' OR outgoing_msg_id = '{$arrayValidData['unique_id']}') AND (outgoing_msg_id = '{$arrayValidData['outgoing_id']}' OR incoming_msg_id = '{$arrayValidData['outgoing_id']}') ORDER BY msg_id DESC LIMIT 1";

        $messageFromUser = parent::getData($queryMessagesUser);

        return $messageFromUser;
    }
}
