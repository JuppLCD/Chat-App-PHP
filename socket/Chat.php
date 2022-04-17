<?php

namespace Socket;

set_time_limit(0);

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

use Class\Message;
use Class\Auth;

require_once __DIR__ . './../php/class/Auth.class.php';
require_once __DIR__ . './../php/class/Message.class.php';

class Chat implements MessageComponentInterface
{
    protected $clients;
    protected $users;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        echo '---sockets corriendo---';
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        $this->users[$conn->resourceId] = ['conn' => $conn];
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);
    }

    public function onMessage(ConnectionInterface $sendingUser,  $data)
    {
        $sendingUser_id = $sendingUser->resourceId;
        $data = json_decode($data);


        $type = $data->type;

        $in_file = $data->in_file;
        // CHAT OR USERS
        $outgoing_id = $data->outgoing_id;

        switch ($type) {
            case 'connection':
                // ! CHAT
                $this->users[$sendingUser_id]['unique_id'] = $outgoing_id;

                if ($in_file === 'CHAT') {
                    $incoming_id = $data->incoming_id;

                    $_mess = new Message;
                    $output = $_mess->getChat($outgoing_id, $incoming_id);

                    $this->users[$sendingUser_id]['conn']->send(json_encode([
                        "type" => $type, "messages" => $output
                    ]));
                    break;
                }


                // ! USERS
                if ($in_file === 'USERS') {
                    $_auth = new Auth;

                    $arrayData = $_auth->getOtherUsers($outgoing_id);

                    $output = "";

                    if (count($arrayData) > 0) {
                        $_mess = new Message;

                        foreach ($arrayData as $userData) {

                            $messageFromUser = $_mess->getLastMessages($userData, $outgoing_id);

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
                    } else {
                        $output .= "No users are available to chat";
                    }

                    $this->users[$sendingUser_id]['conn']->send(json_encode([
                        "type" => $type, "user_list" => $output
                    ]));

                    break;
                }

                // incoming_id
                // Avisar o guardar en DB que el usuario con ese Unique_ID esta conoectado

                // En el momento de inciar se le deberia enviar todo los mensajes de la base de datos con los usiarios y luego conectarlo al socket, evitando el reenvio de solisitudes http para obtener los nuevo mensajes, ya que solo puede haber nuevos mensajes de los usuarios conectados
                break;
            case 'message':
                $message = $data->chat_msg;
                $incoming_id = $data->incoming_id;

                if (empty($message)) {
                    break;
                }

                $_mess = new Message;

                // ! CHAT
                // if ($in_file === 'CHAT') {
                // Ver si el usuario al cual se le quiere enviar el mensaje esta conectado
                $userConected = array_filter($this->users, fn ($user) => $user['unique_id'] === $incoming_id);

                if (count($userConected) === 0) {
                    // Disconnected User
                    $_mess->newMessage($outgoing_id, $incoming_id, $message);

                    // Update chat
                    $sendMessage = '<div class="chat outgoing">
                                    <div class="details">
                                        <p>' . $message . '</p>
                                    </div>
                                </div>';

                    $sendingUser->send(json_encode([
                        "type" => $type, "messages" => $sendMessage
                    ]));
                } else {
                    $_auth = new Auth;

                    $_mess->newMessage($outgoing_id, $incoming_id, $message);

                    $userData = $_auth->getUserBySession($outgoing_id)[0];

                    // Send message
                    $incomingMessage = '<div class="chat incoming">
                                <img src="./../../php/images/' . $userData['img'] . '" alt="' . $userData['fname'] . '_' . $userData['lname'] . '-' . $userData['unique_id'] . '">
                                <div class="details">
                                    <p>' . $message . '</p>
                                </div>
                            </div>';


                    foreach ($this->clients as $clients) {
                        if ($clients->resourceId == key($userConected)) {
                            $clients->send(json_encode([
                                "type" => $type, "messages" => $incomingMessage
                            ]));
                        }
                    }

                    // Update chat
                    $sendMessage = '<div class="chat outgoing">
                                    <div class="details">
                                        <p>' . $message . '</p>
                                    </div>
                                </div>';

                    $sendingUser->send(json_encode([
                        "type" => $type, "messages" => $sendMessage
                    ]));
                }
                //     break;
                // }

                // // ! USERS
                // if ($in_file === 'USERS') {
                // }

                break;
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }
}
