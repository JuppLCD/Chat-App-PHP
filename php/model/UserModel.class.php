<?php

namespace Php\model;

require_once __DIR__ . './../db/Conection.class.php';

use Php\db\Conexion;

class UserModel extends Conexion
{
    public function create(array $data)
    {
        // $data => [fname, lname, email, password, imgName];
        $validData = parent::validCharactersArray($data);

        $unique_id = substr(bin2hex(random_bytes(20)), 0, 20);
        $status = "Active now";

        $idUser = parent::nonQueryId("INSERT INTO users (unique_id, fname, lname, email, password, img, status)             VALUES ('{$unique_id}', '{$validData['fname']}','{$validData['lname']}', '{$validData['email']}', '{$validData['password']}', '{$validData['imgName']}', '{$status}')");

        if ($idUser === 0) {
            return ['error' => 'Error interno del servidor'];
        }
        return ['ok' => true, 'id' => $idUser];
    }

    public function toExist(string $email)
    {
        return parent::getData("SELECT * FROM users WHERE email = '{$email}'");
    }

    public function search(string $searchTerm, $outgoing_id)
    {
        $data = [
            'outgoing_id' => $outgoing_id,
            'searchTerm' => $searchTerm
        ];
        $validData = parent::validCharactersArray($data);

        $queryUsers = "SELECT * FROM users WHERE NOT unique_id = '{$validData['outgoing_id']}' AND (fname LIKE '%{$validData['searchTerm']}%' OR lname LIKE '%{$validData['searchTerm']}%') ";

        $arrayData = parent::getData($queryUsers);
        return $arrayData;
    }

    public function getBySession(string $unique_id)
    {
        $this->unique_id = parent::validCharacters($unique_id);

        $queryUser = "SELECT * FROM users WHERE unique_id = '{$this->unique_id}'";

        $userData = parent::getData($queryUser);
        return $userData;
    }

    public function getOther($outgoing_id)
    {
        $outgoing_id = parent::validCharacters($outgoing_id);

        $queryUsers = "SELECT * FROM users WHERE NOT unique_id = '{$outgoing_id}' ORDER BY user_id DESC";

        $arrayData = parent::getData($queryUsers);

        return $arrayData;
    }

    public function inline(string $unique_id)
    {
        $status = "Active now";
        $afectedRows = parent::nonQuery("UPDATE users SET status = '{$status}' WHERE unique_id='{$unique_id}' ");
        return $afectedRows;
    }

    public function offline(string $unique_id)
    {
        $status = "Offline now";
        $query = "UPDATE users SET status = '{$status}' WHERE unique_id='{$unique_id}'";

        $afectedRows = parent::nonQuery($query);
        return $afectedRows;
    }
}
