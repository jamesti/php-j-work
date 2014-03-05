<?php

define("SISTEMA", "Sistema de Tarefas");
define("VERSAO", "1.0");
define("SESSION_TIME", 600);
define("DB", "db_tarefa");
define("DB_USER", "root");
define("DB_PASSWORD", "");
define("DB_HOST", "localhost");
define("DB_ERROR", "Erro na base de dados!");
define("PATH_FILES", "files/");
define("PATH_IMG", "img/");
define("PATH_CONFIG", "app/config/");
define("PATH_404", PATH_CONFIG . "404.html");
define("REMOTE_IP", filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP));
define("REMOTE_USER_AGENT", filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING));

require_once 'base.php';
require_once 'router.php';
