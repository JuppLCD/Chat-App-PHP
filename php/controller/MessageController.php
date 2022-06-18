<?php

namespace Php\controller;

use Php\class\Auth;
use Php\class\Message;

class MessageController
{
    public function search()
    {
        $outgoing_id = $_SESSION['unique_id'] ?? '';
        $searchTerm =  $_POST['searchTerm'] ?? '';

        if (!empty($outgoing_id) && !empty($searchTerm)) {
            $_auth = new Auth;
            $arrayData = $_auth->searchUser($searchTerm, $outgoing_id);

            $output = "";
            if (count($arrayData) > 0) {
                $_mess = new Message;
                $output = $_mess->getUsersChat($arrayData, $outgoing_id, $output);
            } else {
                $output .= 'No user found related to your search term';
            }
            return $output;
        } else {
            return null;
        }
    }
    public function users()
    {
        $outgoing_id = $_SESSION['unique_id'] ?? '';

        if (!empty($outgoing_id)) {
            $_auth = new Auth;
            $arrayData = $_auth->getOtherUsers($outgoing_id);

            $output = "";
            if (count($arrayData) > 0) {
                $_mess = new Message;
                $output = $_mess->getUsersChat($arrayData, $outgoing_id, $output);
            } else {
                $output .= "No users are available to chat";
            }
            return $output;
        } else {
            return null;
        }
    }
}
