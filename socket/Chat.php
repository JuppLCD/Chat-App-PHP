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
        $outgoing_id = $data->outgoing_id;

        switch ($type) {
            case 'connection':
                $this->users[$sendingUser_id]['unique_id'] = $outgoing_id;
                // ! CHAT
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
                        require_once __DIR__ . './../php/utils/Ui_usersChat.php';
                    } else {
                        $output .= "No users are available to chat";
                    }

                    $this->users[$sendingUser_id]['conn']->send(json_encode([
                        "type" => $type, "user_list" => $output
                    ]));

                    break;
                }

                break;
            case 'message':
                $message = $data->chat_msg;
                $incoming_id = $data->incoming_id;

                if (empty($message)) {
                    break;
                }

                $userConected = array_filter($this->users, fn ($user) => $user['unique_id'] === $incoming_id);
                if (count($userConected) > 0) {
                    $_auth = new Auth;
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
                }
                $this->newMessageAndUpdate($sendingUser, $outgoing_id, $incoming_id, $message, $type);
                break;
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }

    private function newMessageAndUpdate($sendingUser, $outgoing_id, $incoming_id, $message, $type)
    {
        $_mess = new Message;

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
    }
}
