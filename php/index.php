<?php
require_once dirname(__DIR__) . './vendor/autoload.php';
session_start();

use Php\class\Response;
use Php\controller\UserController;
use Php\controller\MessageController;

$URL = explode(
    '=',
    $_SERVER["REDIRECT_QUERY_STRING"]
)[1];

if ($URL) {

    $list = ['logout', 'users', 'login', 'signup', 'search'];

    // 404
    if (!in_array($URL, $list)) {
        $_resClass = new Response;

        header('Content-Type: application/json');
        $resData = $_resClass->err('', 404);
        echo json_encode($resData);
        exit;
    }

    // API
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($URL === 'logout') {
                $USER_CONTROLLER = new UserController();
                $USER_CONTROLLER->logout();
                exit;
            } else if ($URL === 'users') {
                $MESSAGE_CONTROLLER = new MessageController();
                $output = $MESSAGE_CONTROLLER->users();

                if (is_null($output)) {
                    header("location: ../src/view/login");
                    exit;
                }
                echo $output;
                exit;
            }
            break;
        case 'POST':
            if ($URL === 'login') {
                $USER_CONTROLLER = new UserController();
                $res = $USER_CONTROLLER->login();

                header('Content-Type: application/json');
                echo json_encode($res);
                exit;
            } else if ($URL === 'signup') {
                $USER_CONTROLLER = new UserController();
                $res = $USER_CONTROLLER->signup();

                header('Content-Type: application/json');
                echo json_encode($res);
                exit;
            } else if ($URL === 'search') {
                $MESSAGE_CONTROLLER = new MessageController();
                $output = $MESSAGE_CONTROLLER->search();

                if (is_null($output)) {
                    header("location: ../src/view/login");
                    exit;
                }
                echo $output;
                exit;
            }
            break;
    }
}
