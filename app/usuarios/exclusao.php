<?php 

$rs = base_exclusao("tbl_usuario", "id_usuario", $_GET['id']);

if ($rs) {
	$_SESSION['msg'] = 005;
	header('location: ?view=usuarios');
}

?>