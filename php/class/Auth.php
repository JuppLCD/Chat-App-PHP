<?php

namespace Php\class;

use Php\model\UserModel;;

use Php\class\Response;

class Auth
{
    public string $unique_id;
    private Response $_resClass;
    private UserModel $_userModel;

    public function signup($fname, $lname, $email, $password, $img)
    {
        $this->_resClass = new Response;
        $this->_userModel = new UserModel;

        $userExist = $this->validEmail($email);

        if ($this->_resClass->response['status'] !== 'ok') {
            return $this->_resClass->response;
        }

        if (count($userExist) !== 0) {
            return $this->_resClass->err("{$email} - This email already exist!", 200);
        }

        $this->validImgAndMove($img);

        if ($this->_resClass->response['status'] !== 'ok') {
            return $this->_resClass->response;
        }

        $data = ['imgName' => $this->imgName, 'email' => $email, 'password' => $password, 'fname' => $fname, 'lname' => $lname];

        $newUser = $this->_userModel->create($data);

        if (isset($newUser['error']) === 'Error interno del servidor') {
            return $this->_resClass->err("", 500);
        }

        $this->unique_id = $newUser['unique_id'];

        return $this->_resClass->response = [
            'status' => "ok",
            "result" => "success"
        ];
    }

    private function validEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->_resClass->err("{$email} is not a valid email!", 200);
        }
        $userExist = $this->_userModel->toExist($email);
        return $userExist;
    }

    private function validImgAndMove($img)
    {
        $img_name = $img['name'];
        $img_type = $img['type'];


        $img_explode = explode('.', $img_name);
        $img_ext = end($img_explode);

        $extensions = ["jpeg", "png", "jpg"];

        if (!in_array($img_ext, $extensions)) {
            return $this->_resClass->err("Please upload an image file - jpeg, png, jpg", 400);
        }

        $types = ["image/jpeg", "image/jpg", "image/png"];

        if (!in_array($img_type, $types)) {
            return $this->_resClass->err("Please upload an image file - jpeg, png, jpg", 400);
        }

        $time = time();
        $new_img_name = $time . '_' . $img_name;

        $tmp_name = $img['tmp_name'];


        if (!move_uploaded_file($tmp_name, 'images/' . $new_img_name)) {
            return $this->_resClass->err("Could not save image", 500);
        }

        $this->imgName = $new_img_name;
    }

    public function login($email, $password)
    {
        $this->_resClass = new Response;
        $this->_userModel = new UserModel();

        $userExist = $this->validEmail($email);

        if ($this->_resClass->response['status'] !== 'ok') {
            return $this->_resClass->response;
        }

        if (count($userExist) === 0 || $userExist[0]['password'] !== $password) {
            return $this->_resClass->err("", 200);
        }

        $this->unique_id = $userExist[0]['unique_id'];

        $afectedRows = $this->_userModel->inline($userExist[0]['unique_id']);

        // if (!$afectedRows) {
        //     return $this->_resClass->err("Something went wrong. Please try again!", 500);
        // }

        return $this->_resClass->response = [
            'status' => "ok",
            "result" => "success"
        ];
    }

    public function logout(string $unique_id)
    {
        $this->_resClass = new Response;
        $this->_userModel = new UserModel();

        if (!isset($unique_id)) {
            return $this->_resClass->err('', 400);
        }

        $afectedRows = $this->_userModel->offline($unique_id);

        if (!$afectedRows) {
            return $this->_resClass->err('', 500);
        }

        return $this->_resClass->response;
    }

    public function getOtherUsers($outgoing_id)
    {
        $_userModel = new UserModel();
        $arrayData = $_userModel->getOther($outgoing_id);
        return $arrayData;
    }

    public function searchUser($searchTerm, $outgoing_id)
    {
        $_userModel = new UserModel();
        $arrayData = $_userModel->search($searchTerm, $outgoing_id);
        return $arrayData;
    }

    public function getUserBySession($unique_id)
    {
        $_userModel = new UserModel();
        $userData = $_userModel->getBySession($unique_id);
        return $userData;
    }
}
