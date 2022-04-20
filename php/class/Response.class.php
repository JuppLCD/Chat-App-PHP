<?php

namespace Php\class;

class Respuestas
{
    public  $response = [
        'status' => "ok",
        "result" => array()
    ];

    protected $errors_msg = [
        '200' => 'Datos incorrectos',
        '400' => 'Datos enviados incompletos o con formato incorrecto',
        '405' => 'Metodo no permitido',
        '401' => 'No autorizado',
        '405' => 'Metodo no permitido',
        '500' => 'Error interno del servidor'

    ];

    public function err($valor, $type)
    {
        $valor = $valor == '' ?  $this->errors_msg["$type"] : $valor;

        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "$type",
            "error_msg" => $valor
        );
        return $this->response;
    }
}
