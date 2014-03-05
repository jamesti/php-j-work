<?php

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') != "POST") {
    $_SESSION['msg'] = 002;
    header('location: ?');
    exit;
}

$filtros = array(
    "token" => array("filter" => FILTER_SANITIZE_STRING),
    "login" => array("filter" => FILTER_SANITIZE_STRING),
    "senha" => array("filter" => FILTER_SANITIZE_STRING)
);

$result = filter_input_array(INPUT_POST, $filtros);

$formToken = $_SESSION['form_token'];

unset($_SESSION['form_token']);

if (!$result || $result['token'] != $formToken) {
    $_SESSION['msg'] = 002;
    header('location: ?');
    exit;
} else {
    
    //Validar Login e Senha aqui
    unset($result['token']);
    
    $usuario = base_consultarUsuario("tbl_usuario", array_flip($result), $result);
    
    if (!$usuario)
    {
        $_SESSION['msg'] = 001;
        header('location: ?');
        exit;
    }
    
    unset($usuario['senha']);
    
    $_SESSION['usuario'] = $usuario;    
    $_SESSION['acesso'] = 'passou.' . md5(session_name() . $_SESSION['token']);
    header('location: ?');
}