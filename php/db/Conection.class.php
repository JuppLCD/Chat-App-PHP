<?php

class Conexion
{

    private $server;
    private $user;
    private $password;
    private $database;
    private $port;
    private $conexion;

    function __construct()
    {
        $dataList = $this->dataConection();
        $this->server = $dataList['server'];
        $this->user = $dataList['user'];
        $this->password = $dataList['password'];
        $this->database = $dataList['database'];
        $this->port = $dataList['port'];

        $this->conexion = new mysqli(
            $this->server,
            $this->user,
            $this->password,
            $this->database,
            $this->port
        );
        if ($this->conexion->connect_errno) {
            echo "Error connecting to DB";
            die();
        }
    }

    private function dataConection()
    {
        $direccion = dirname(__FILE__);
        $jsondata = file_get_contents($direccion . "/" . "config.json");
        $data = json_decode($jsondata, true);
        return $data['conexion'];
    }

    private function convertToUTF8($array)
    {
        array_walk_recursive($array, function (&$item, $key) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
            }
        });
        return $array;
    }

    public function getData(string $sqlstr)
    {
        $results = $this->conexion->query($sqlstr);
        $resultArray = array();
        foreach ($results as $key) {
            $resultArray[] = $key;
        }
        return $this->convertToUTF8($resultArray);
    }

    public function nonQuery(string $sqlstr)
    {
        $this->conexion->query($sqlstr);
        return $this->conexion->affected_rows;
    }

    public function nonQueryId(string $sqlstr)
    {
        $this->conexion->query($sqlstr);
        $filas = $this->conexion->affected_rows;
        if ($filas >= 1) {
            return $this->conexion->insert_id;
        } else {
            return 0;
        }
    }

    public function validCharacters(string $string)
    {
        return $this->conexion->real_escape_string($string);
    }

    protected function encriptar(string $string)
    {
        return md5($string);
    }
}
