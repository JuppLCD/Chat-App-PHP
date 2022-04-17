<?php

namespace Class;

include_once dirname(__FILE__) . "./Response.class.php";
require_once(dirname(__FILE__) . './../db/Conection.class.php');


use DB\Conexion;
use Class\Respuestas;


class Auth extends Conexion
{
    private $email = '';
    private $password = '';
    private $fname = '';
    private $lname = '';
    public $unique_id = '';

    public function signup($fname, $lname, $email, $password, $img)
    {
        $_resClass = new Respuestas;

        $this->validCharactersSingup($email, $password, $fname, $lname);

        $userExist = $this->validEmail($_resClass);

        if ($_resClass->response['status'] !== 'ok') {
            return $_resClass->response;
        }

        if (count($userExist) !== 0) {
            return $_resClass->err("{$this->email} - This email already exist!", 200);
        }

        $this->validImgAndMove($img, $_resClass);

        if ($_resClass->response['status'] !== 'ok') {
            return $_resClass->response;
        }

        $this->createUser($_resClass);

        return $_resClass->response = [
            'status' => "ok",
            "result" => "success"
        ];
    }

    private function validEmail($_resClass)
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return $_resClass->err("{$this->email} is not a valid email!", 200);
        }

        $userExist = parent::getData("SELECT * FROM users WHERE email = '{$this->email}'");

        return $userExist;
    }

    private function validImgAndMove($img, $_resClass)
    {
        $img_name = $img['name'];
        $img_type = $img['type'];


        $img_explode = explode('.', $img_name);
        $img_ext = end($img_explode);

        $extensions = ["jpeg", "png", "jpg"];

        if (!in_array($img_ext, $extensions)) {
            return $_resClass->err("Please upload an image file - jpeg, png, jpg", 400);
        }

        $types = ["image/jpeg", "image/jpg", "image/png"];

        if (!in_array($img_type, $types)) {
            return $_resClass->err("Please upload an image file - jpeg, png, jpg", 400);
        }

        $time = time();
        $new_img_name = $time . '_' . $img_name;

        $tmp_name = $img['tmp_name'];

        if (!move_uploaded_file($tmp_name, "images/" . $new_img_name)) {
            return $_resClass->err("Could not save image", 500);
        }

        $this->imgName = $new_img_name;
    }

    private function validCharactersLogin($email, $password)
    {
        $email = parent::validCharacters($email);
        $password = parent::validCharacters($password);

        $this->email = $email;
        $this->password = parent::encriptar($password);
    }

    private function validCharactersSingup($email, $password, $fname, $lname)
    {
        $fname = parent::validCharacters($fname);
        $lname = parent::validCharacters($lname);
        $this->validCharactersLogin($email, $password);

        $this->fname = $fname;
        $this->lname = $lname;
    }

    private function createUser($_resClass)
    {
        $unique_id = substr(bin2hex(random_bytes(20)), 0, 20);
        $status = "Active now";

        $idUser = parent::nonQueryId("INSERT INTO users (unique_id, fname, lname, email, password, img, status)             VALUES ('{$unique_id}', '{$this->fname}','{$this->lname}', '{$this->email}', '{$this->password}', '{$this->imgName}', '{$status}')");

        if ($idUser === 0) {
            return $_resClass->err("Something went wrong. Please try again!", 500);
        }
    }

    public function login($email, $password)
    {
        $_resClass = new Respuestas;

        $this->validCharactersLogin($email, $password);

        $userExist = $this->validEmail($_resClass);

        if ($_resClass->response['status'] !== 'ok') {
            return $_resClass->response;
        }

        if (count($userExist) === 0) {
            return $_resClass->err("$email - This email not exist!", 200);
        }

        if ($userExist[0]['password'] !== $this->password) {
            return $_resClass->err("Email or Password is Incorrect!", 200);
        }

        $this->unique_id = $userExist[0]['unique_id'];

        $status = "Active now";
        $verificar = parent::nonQuery("UPDATE users SET status = '{$status}' WHERE unique_id='{$this->unique_id}' ");

        // if (!$verificar) {
        //     return $_resClass->err("Something went wrong. Please try again!", 500);
        // }

        return $_resClass->response = [
            'status' => "ok",
            "result" => "success"
        ];
    }

    public function logout(string $unique_id)
    {
        $_resClass = new Respuestas;

        $unique_id = parent::validCharacters($unique_id);

        if (!isset($unique_id)) {
            return $_resClass->err('', 400);
        }
        $status = "Offline now";
        $query = "UPDATE users SET status = '{$status}' WHERE unique_id='{$unique_id}'";

        $afectedRows = parent::nonQuery($query);

        if (!$afectedRows) {
            return $_resClass->err('', 500);
        }

        return $_resClass->response;
    }

    public function getOtherUsers($outgoing_id)
    {
        $outgoing_id = parent::validCharacters($outgoing_id);

        $queryUsers = "SELECT * FROM users WHERE NOT unique_id = '{$outgoing_id}' ORDER BY user_id DESC";

        $arrayData = parent::getData($queryUsers);

        return $arrayData;
    }

    public function searchUser($searchTerm, $outgoing_id)
    {
        $searchTerm = parent::validCharacters($searchTerm);
        $outgoing_id = parent::validCharacters($outgoing_id);

        $queryUsers = "SELECT * FROM users WHERE NOT unique_id = '{$outgoing_id}' AND (fname LIKE '%{$searchTerm}%' OR lname LIKE '%{$searchTerm}%') ";

        $arrayData = parent::getData($queryUsers);
        return $arrayData;
    }

    public function getUserBySession($unique_id)
    {
        $this->unique_id = parent::validCharacters($unique_id);

        $queryUser = "SELECT * FROM users WHERE unique_id = '{$this->unique_id}'";

        $userData = parent::getData($queryUser);
        return $userData;
    }
}
