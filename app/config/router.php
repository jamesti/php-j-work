<?php

/**
 * Roda a Aplicação com o FrameWork Funcional/Procedural do James Moreira.
 */
function run() {

    router_setSession();

    $result = router_filterController();

    router_getMsg();

    if (!$result['action'] && !$result['view'] && !base_validarAcesso()) {
        router_getFile(PATH_CONFIG, 'login.php');
    } elseif ($result['action'] && isset($_SESSION['form_token']) && !base_validarAcesso()) {
        if ($result['action'] == md5($_SESSION['form_token'] . $_SESSION['token'])) {
            router_getFile(PATH_CONFIG, 'validar.php');
        } else {
            router_getFile(PATH_CONFIG, 'login.php');
        }
    } elseif (base_validarAcesso()) {
        if (isset($result['action']) && $result['action'] == 'logout') {
            base_logout();
        }
        router_getFile(PATH_CONFIG, 'menu.php');
        if (isset($result['view']) && isset($result['action'])) {
            router_getFile('app/' . $result['view'] . '/', $result['action'] . '.php');
        } elseif (isset($result['view'])) {
            router_getFile('app/' . $result['view'] . '/', 'consulta.php');
        } else {
            router_getFile(PATH_CONFIG, 'home.php');
        }
    } else {
        router_getFile(PATH_CONFIG, 'login.php');
    }
}

/**
 * Pegar mensagens guardadas dentro de uma sessão com base na Mensageria.
 */
function router_getMsg() {
    if (isset($_SESSION ['msg'])) {
        echo base_mensageria($_SESSION ['msg']);
        unset($_SESSION['msg']);
    }
}

/**
 * Valida os filtros de Query String para evitar Ataques XSS e SQL Injection.
 * @return Array Validado.
 */
function router_filterController() {
    $filtros = array("action" => array("filter" => FILTER_SANITIZE_STRING),
        "view" => array("filter" => FILTER_SANITIZE_STRING),
        "id" => array("filter" => FILTER_VALIDATE_INT),
        "pag" => array("filter" => FILTER_VALIDATE_INT),
        "order" => array("filter" => FILTER_VALIDATE_INT));

    return $result = filter_input_array(INPUT_GET, $filtros);
}

/**
 * Setar as configurações de sessão do Usuário do Sistema, incluir Token e SessionTime.
 */
function router_setSession() {
    $acesso = md5(date('d') . REMOTE_IP . REMOTE_USER_AGENT);

    session_id($acesso + SESSION_TIME);
    session_name($acesso);
    session_cache_expire(SESSION_TIME / 3);
    session_start();

    router_setSessionTime();
    router_setSessionToken();
}

/**
 * Setar o Token de sessão para o usuário ou visitante do sistema.
 */
function router_setSessionToken() {
    if (!isset($_SESSION['token'])) {
        $_SESSION['token'] = md5('phpsecurity' . REMOTE_IP . REMOTE_USER_AGENT);
    }
}

/**
 * Setar o tempo de Sessão de cada página do sistema de acordo com o SESSION_TIME.
 */
function router_setSessionTime() {
    if (!isset($_SESSION['expire'])) {
        $_SESSION['expire'] = time() + SESSION_TIME;
    } else {
        if (($_SESSION['expire'] - time()) <= 0) {
            session_unset();
            session_destroy();
        } else {
            $_SESSION['expire'] = time() + SESSION_TIME;
        }
    }
}

/**
 * Pegar arquivo de acordo com o seu Caminho e arquivo com sua extensão.
 * @param string $path Ex: files/foo/bar/
 * @param string $file Ex: bar.php
 */
function router_getFile($path, $file) {

    $filename = $path . $file;

    if (!file_exists($filename)) {
        require_once PATH_404;
    } else {
        require_once $path . $file;
    }
}
