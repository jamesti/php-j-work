<?php 

$campos = array("id_usuario","login","email",
		"case perfil when 'A' then 'Administrador' else 'Funcionário' end",
"DATE_FORMAT(dt_criacao,'%d/%m/%Y')");

$colunas = array("Código","Login","E-mail","Perfil","Data de Criação");

$filtros = array('login','email');

echo base_htmlAutoFormConsultar("tbl_usuario", $campos, $colunas, "usuarios", $filtros);
