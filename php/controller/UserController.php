<?php

namespace Php\controller;

use Php\class\Response;
use Php\class\Auth;

class UserController
{
    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!empty($email) && !empty($password)) {
            $_auth = new Auth;

            $resData = $_auth->login($email, $password);

            if (isset($resData["result"]["error_id"])) {
                $responseCode = $resData["result"]["error_id"];
                http_response_code($responseCode);
            } else {
                $_SESSION['unique_id'] = $_auth->unique_id;
                http_response_code(200);
            }

            return $resData;
        } else {
            $_resClass = new Response;
            return $_resClass->err('', 400);
        }
    }

    public function logout()
    {
        $logout_id = $_GET['logout_id'] ?? '';

        if (isset($_SESSION['unique_id']) && !empty($logout_id)) {
            $_auth = new Auth;

            $resData = $_auth->logout($logout_id);

            if (isset($resData["result"]["error_id"])) {
                header("location: ../src/view/users");
            } else {
                session_unset();
                session_destroy();
                header("location: ../src/view/login");
            }
        } else {
            header("location: ../src/view/login");
        }
    }
    public function signup()
    {
        $fname = $_POST['fname'] ?? '';
        $lname = $_POST['lname'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!empty($fname) && !empty($lname) && !empty($email) && !empty($password) && isset($_FILES['image'])) {
            $_auth = new Auth;

            $resData = $_auth->signup($fname, $lname, $email, $password, $_FILES['image']);

            if (isset($resData["result"]["error_id"])) {
                $responseCode = $resData["result"]["error_id"];
                http_response_code($responseCode);
            } else {
                $unique_id = $_auth->unique_id;
                $_SESSION['unique_id'] = $unique_id;
                http_response_code(200);
            }

            return $resData;
        } else {
            $_resClass = new Response;
            $resData = $_resClass->err('Todos los campos son obligatorios', 400);

            return $resData;
        }
    }
}
