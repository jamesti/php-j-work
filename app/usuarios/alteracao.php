<?php
if (filter_input ( INPUT_SERVER, 'REQUEST_METHOD' ) == "POST") {
	
	// Alteração	
	$filtros = array (
			"id" => array (
					"filter" => FILTER_SANITIZE_STRING
			),
			"login" => array (
					"filter" => FILTER_SANITIZE_STRING 
			),
			"senha" => array (
					"filter" => FILTER_SANITIZE_STRING 
			),
			"email" => array (
					"filter" => FILTER_VALIDATE_EMAIL,
					FILTER_SANITIZE_EMAIL 
			),
			"perfil" => array (
					"filter" => FILTER_SANITIZE_STRING 
			) 
	);
	
	$result = filter_input_array(INPUT_POST, $filtros);
	
	if ($result) {
		
		$senha = $result['senha'];
		
		$campos = array('id_usuario','login','senha','email','perfil');
		
		//Compara se alterou a senha
		if ($senha == "") {
			unset($result['senha']);
			$campos = array('id_usuario','login','email','perfil');
		} else {
			$result['senha'] = md5($senha);
		}
		
		$rs = base_alterar('tbl_usuario', $campos, $result);
		
		if ($rs) {
			$_SESSION['msg'] = 004;
			header('location: ?view=usuarios');
		}
	}
}

$campos = array("id_usuario","login","email",
		"perfil",
		"DATE_FORMAT(dt_criacao,'%d/%m/%Y')");

$linha = base_consultar_id("tbl_usuario", $campos, $_GET['id']);

?>

<form method="post"
	action="<?=filter_var('?action=alteracao&view=usuarios', FILTER_SANITIZE_STRING) ?>"
	class="form-horizontal">
	<fieldset>

		<!-- Form Name -->
		<legend>Alteração de Usuários</legend>

                <input type="hidden" name="id" value="<?=$linha[0] ?>" />
                
		<!-- Text input-->
		<div class="control-group">
			<label class="control-label" for="login">Login:</label>
			<div class="controls">
				<input id="login" name="login" type="text"
					value="<?=$linha[1] ?>"
					placeholder="Login do Usuário" class="input-xlarge" required>

			</div>
		</div>

		<!-- Password input-->
		<div class="control-group">
			<label class="control-label" for="senha">Senha:</label>
			<div class="controls">
				<input id="senha" name="senha" type="password"
					value=""
					placeholder="Alterar Senha" class="input-xlarge" >

			</div>
		</div>

		<!-- Text input-->
		<div class="control-group">
			<label class="control-label" for="email">E-mail:</label>
			<div class="controls">
				<input id="email" name="email" type="email"
					value="<?=$linha[2] ?>"
					placeholder="E-mail do Usuário" class="input-xlarge" required>

			</div>
		</div>

		<!-- Select Basic -->
		<div class="control-group">
			<label class="control-label" for="perfil">Perfil:</label>
			<div class="controls">
				<select id="perfil" name="perfil" class="input-xlarge">
					<option value="A" <?php if ($linha[3] == 'A') {echo "selected";} ?>>Administrador</option>
					<option value="F" <?php if ($linha[3] == 'F') {echo "selected";} ?>>Funcionário</option>
				</select>
			</div>
		</div>

		<!-- Button -->
		<div class="control-group">
			<label class="control-label" for="singlebutton"></label>
			<div class="controls">
				<button id="singlebutton" name="singlebutton"
					class="btn btn-primary">Alterar</button>
			</div>
		</div>

	</fieldset>
</form>