<?php

namespace Socket;

set_time_limit(0);

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

use Php\class\Message;
use Php\class\Auth;
use Php\class\FormatMessage;

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
        $this->users[$sendingUser_id]['unique_id'] = $outgoing_id;

        switch ($type) {
            case 'connection':
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

                    if (count($arrayData) === 0) {
                        $output .= "No users are available to chat";
                    } else {
                        $_mess = new Message;
                        $output = $_mess->getUsersChat($arrayData, $outgoing_id, $output);
                    }

                    $sendingUser->send(json_encode([
                        "type" => $type, "user_list" => $output, 'unique' => $outgoing_id, 'id' => $sendingUser_id
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

                $_mess = new Message;
                $_formatMessage = new FormatMessage;

                $userConected = array_filter($this->users, fn ($user) => $user['unique_id'] === $incoming_id);
                if (count($userConected) > 0) {
                    $_auth = new Auth;

                    $userData = $_auth->getUserBySession($outgoing_id)[0];

                    // Send message
                    $incomingMessage = $_formatMessage->incomingMessage($userData, $message);

                    foreach ($this->clients as $clients) {
                        if ($clients->resourceId == key($userConected)) {
                            $clients->send(json_encode([
                                "type" => $type, "messages" => $incomingMessage
                            ]));
                        }
                    }
                }

                $_mess->newMessage($outgoing_id, $incoming_id, $message);

                // Update chat
                $sendMessage = $_formatMessage->sendMessage($message);

                $sendingUser->send(json_encode([
                    "type" => $type, "messages" => $sendMessage
                ]));
                break;
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }
}
