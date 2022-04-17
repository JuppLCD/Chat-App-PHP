<?php
session_start();

use Class\Auth;

$outgoing_id = $_SESSION['unique_id'] ?? '';
$searchTerm =  $_POST['searchTerm'] ?? '';

if (!empty($outgoing_id) && !empty($searchTerm)) {
    include_once dirname(__FILE__) . "./class/Auth.class.php";
    $_auth = new Auth;

    $arrayData = $_auth->searchUser($searchTerm, $outgoing_id);

    $output = "";

    if (count($arrayData) > 0) {
        include_once dirname(__FILE__) . "./utils/Ui_usersChat.php";
    } else {
        $output .= 'No user found related to your search term';
    }
    echo $output;
}
