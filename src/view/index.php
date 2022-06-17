<?php
require dirname(__DIR__) . '../../vendor/autoload.php';

if (isset($_GET['route'])) {

    $params = explode('/', $_GET['route']);

    $list = ['chat.php', 'users.php', 'login.php', 'register.php'];
    $file = dirname(__DIR__) . '/view/' . $params[0];

    if (!in_array($params[0], $list) || $params === 'index.php') {
        require dirname(__DIR__) . '/view/register.php';
        exit;
    }

    if (is_readable($file)) {
        require $file;
        exit;
    } else {
        echo 'El archivo de la ruta no esta creado';
    }
} else {
    // ESTO NUNCA DEBERIA PASAR, CREO...
    echo '';
    exit;
}
