<?php

namespace Php\class;

use Php\model\MessageModel;

use Php\class\Response;
use Php\class\FormatMessage;

class Message
{
    public function newMessage($outgoing_id, $incoming_id, $message)
    {
        $_resClass = new Response;
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
            return '';
        }

        $_formatMessage = new FormatMessage;
        return  $_formatMessage->getChat($output, $arrayData, $data);
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
                continue;
            }

            $_formatMessage = new FormatMessage;
            $output .= $_formatMessage->getAllChats($outgoing_id, $userData);
        };
        return $output;
    }
}
