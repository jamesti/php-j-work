<?php

if (base_requestMethod("post")) {

    // Cadastro	
    $filtros = array(
        "login" => array(
            "filter" => FILTER_SANITIZE_STRING
        ),
        "senha" => array(
            "filter" => FILTER_SANITIZE_STRING
        ),
        "email" => array(
            "filter" => FILTER_VALIDATE_EMAIL,
            FILTER_SANITIZE_EMAIL
        ),
        "perfil" => array(
            "filter" => FILTER_SANITIZE_STRING
        )
    );

    $result = filter_input_array(INPUT_POST, $filtros);

    if ($result) {

        $senha = $result['senha'];

        $result['senha'] = md5($senha);

        $result['dt_criacao'] = date('Y-m-d H:i:s');

        $campos = array('login', 'senha', 'email', 'perfil', 'dt_criacao');

        base_cadastrar('tbl_usuario', $campos, $result);
    }
}

echo base_breadcrumbs();

echo base_formBegin("post");

echo base_formTextInput(array('login', 'Login', 'Login do Usuario', 'text', 'required'));

echo base_formTextInput(array('senha', 'Senha', 'Senha do Usuario', 'password', 'required'));

echo base_formTextInput(array('email', 'E-mail', 'E-mail do Usuario', 'email', 'required'));

echo base_formSelectBasic(array('perfil', 'Perfil'), array('A' => 'Administrador', 'F' => 'Funcionario'));

echo base_formButtonSubmit("Cadastrar");

echo base_formEnd();
