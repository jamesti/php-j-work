<?php

function base_conectar() {
    $con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB);

    if (mysqli_connect_errno()) {
        exit(DB_ERROR);
    } else {
        return $con;
    }
}

/**
 * Consulta Usuário no banco com base em seu login e senha
 * @param string $tabela Especificar Tabela de Usuários.
 * @param array $campos Ex: login,senha
 * @param array $valores Ex: login,senha
 * @return Array de valores da Tabela especificada de usuários.
 */
function base_consultarUsuario($tabela, $campos, $valores) {
    $con = base_conectar();

    $query = "select * from $tabela where " . current($campos) . " = '" . current($valores) . "' and "
            . next($campos) . " = md5('" . next($valores) . "')";

    $result = mysqli_query($con, $query);

    if (mysqli_error($con)) {
        exit(DB_ERROR);
    } else {
        $linha = mysqli_fetch_assoc($result);
        return $linha;
    }
}

function base_validarCampoDB($tabela, $campos, array $valores) {
    $con = base_conectar();

    $cps = implode(",", $campos);

    reset($campos);
    reset($valores);
    
    $where = "";
    
    foreach ($campos as $value) {
        $where .= "$value = '".current($valores)."' or ";
        next($valores);
    }
    
    $where = substr($where, 0, -3);
    
    $query = "select $cps from $tabela where $where";

    $result = mysqli_query($con, $query);

    if (mysqli_error($con)) {
        exit(DB_ERROR);
    } else {
        $linha = mysqli_fetch_array($result);
        return $linha;
    }
}

function base_consultar_id($tabela, $campos, $id) {
    $con = base_conectar();

    $cps = implode(",", $campos);

    reset($campos);

    $query = "select $cps from $tabela where " . current($campos) . " = $id";

    $result = mysqli_query($con, $query);

    if (mysqli_error($con)) {
        exit(DB_ERROR);
    } else {
        $linha = mysqli_fetch_array($result);
        return $linha;
    }
}

function base_paginacao($numRegs, $pag) {
    
    if ($numRegs > REGISTROS) {
        $pags = $numRegs / REGISTROS;
        if (is_real($pags)) {
            $pags++;
            $pags = intval($pags);
        }
    } else {
        return;
    }
    
    $pagination = "\n<div class='pagination'>
                      <ul>\n";
    
    $pagina = $pag['pag'] == NULL ? 1 : $pag['pag'];
    
    for ($i = 1; $i <= $pags; $i++) {
        if ($pagina == $i) {
            $pagination .= "    <li class='active'><a href='?view={$pag['view']}&pag=$i'>$i</a></li>\n";
        } else {
            $pagination .= "    <li><a href='?view={$pag['view']}&pag=$i'>$i</a></li>\n";
        }
    }
    
    $pagination .= "   </ul>
                    </div>\n";
    
    return $pagination;
}

function base_consultar($tabela, $campos, $colunas, $view, array $filtro = null) {
    $con = base_conectar();

    $cps = implode(",", $campos);

    $query = "select $cps from $tabela " . base_filtrosConsulta($filtro) . " LIMIT 1000";

    $regsResult = mysqli_query($con, $query);
    
    $regs = mysqli_num_rows($regsResult);
    
    $pag = router_filterController();
        
    $pagina = $pag['pag'] == NULL || $pag['pag'] == 1 ? 0 : $pag['pag'] - 1;
    
    $query = substr($query, 0, -11);
    
    $query .= " LIMIT " . $pagina * REGISTROS . "," . REGISTROS;
    
    $result = mysqli_query($con, $query);

    if (mysqli_error($con)) {
        exit(DB_ERROR);
    } else {
        $table = '<table class="table table-hover">
				<thead>
					<tr>';
        foreach ($colunas as $value) {
            $table .= "<th>$value</th>";
        }
        $table .= "<th>Ações</th>
				</tr>
				</thead>
					<tbody>";
        while ($linha = mysqli_fetch_array($result)) {
            $table .= "<tr>";
            foreach ($campos as $value) {
                $table .= "<td>{$linha[$value]}</td>";
            }
            $table .= "<td>" . base_botaoEditar($view, $linha[0]) . " | " . base_botaoExcluir($view, $linha[0]) . "</td>";
            $table .= "</tr>";
        }
        $table .= "</tbody>
				</table>\n";

        $pagination = base_paginacao($regs, $pag);
        
        return $table . $pagination;
    }
}

/**
 * Filtros para a base_consulta automático!
 * @param array $filtro Ex: array('nome','descricao')
 * @return string
 */
function base_filtrosConsulta(array $filtro = null) {

    if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'POST') {

        $result = filter_input(INPUT_POST, 'busca', FILTER_SANITIZE_STRING);

        if (!$result) {
            return "";
        }

        if (is_array($filtro)) {
            $fil = " where ";
            foreach ($filtro as $value) {
                $fil .= "$value like '%$result%' or ";
            }
            $fil = substr($fil, 0, -3);
            $query = $fil;

            return $query;
        }

        return "";
    }
}

function base_botaoEditar($view, $cod) {
    return "<a href='?action=alteracao&view=$view&id=$cod'><i class='icon-edit'></i> Editar</a>";
}

function base_botaoExcluir($view, $cod) {
    return "<a href='#' onclick='excluir(\"?action=exclusao&view=$view&id=$cod\")' ><i class='icon-remove'></i> Excluir</a>";
}

function base_cadastrar($tabela, $campos, $valores) {
    $con = base_conectar();

    $cps = implode(",", $campos);
    $vls = implode("','", $valores);

    $query = "INSERT INTO $tabela ($cps) VALUES('$vls')";

    mysqli_query($con, $query);

    if (mysqli_error($con)) {
        exit(DB_ERROR);
    } else {
        mysqli_close($con);
        $_SESSION['msg'] = 003;
        $result = router_filterController();
        header('location: ?view=' . $result['view']);
        return true;
    }
}

function base_exclusao($tabela, $campoId, $id) {
    $con = base_conectar();

    $query = "DELETE FROM $tabela where $campoId = $id";

    mysqli_query($con, $query);

    if (mysqli_error($con)) {
        exit(DB_ERROR);
    } else {
        mysqli_close($con);
        return true;
    }
}

/**
 * <h1>Base para alteração dos cadastros</h1>
 * <p>Nota: O primeiro Campo e Valor é o código.</p>
 * 
 * @param
 *        	String de $tabela
 * @param
 *        	Array de Campos da Tabela $campos
 * @param
 *        	Array de Valores via POST $valores
 * @return True se tudo OK! Senão Erro no Banco
 */
function base_alterar($tabela, $campos, $valores) {
    $con = base_conectar();

    $query = "UPDATE $tabela SET ";

    foreach ($campos as $campo) {
        $query .= "$campo = '" . current($valores) . "', ";
        next($valores);
    }

    $query = substr($query, 0, - 2);

    reset($valores);

    $query .= " Where " . current($campos) . " = " . current($valores);

    mysqli_query($con, $query);

    if (mysqli_error($con)) {
        exit(DB_ERROR);
    } else {
        mysqli_close($con);
        return true;
    }
}

function base_validarAcesso() {
    if (!isset($_SESSION ['acesso'])) {
        return false;
    }

    $result = explode('.', $_SESSION ['acesso']);

    if (end($result) == md5(session_name() . $_SESSION ['token'])) {
        return true;
    }
}

function base_logout() {
    session_unset();

    session_destroy();

    header('location: ?');
}

function base_breadcrumbs() {

    $result = router_filterController();

    return '<ul class="breadcrumb">
            <li><a href="?view=' . $result['view'] . '">' . ucfirst($result['view']) . '</a> <span class="divider">/</span></li>
            <li class="active">' . ucfirst($result['action'] == NULL ? 'Consulta' : $result['action']) . '</li>
          </ul>';
}

function base_formConsulta($view) {
    return '<form class="form-search" method="post" action="?view=' . $view . '">
            <input type="text" name="busca" class="input-medium search-query">
            <button type="submit" class="btn">Buscar</button>
          </form>';
}

function base_formBotaoCadastrar($view) {
    return '<a href="?action=cadastro&view=' . $view . '" class="btn btn-primary" type="button">Cadastrar Novo ' . ucfirst(substr($view, 0, -1)) . '</a>';
}

function base_htmlAutoFormConsultar($tabela, $campos, $colunas, $view, array $filtros = NULL) {
    $html = base_breadcrumbs();

    if ($filtros != NULL) {
        $html .= base_formConsulta($view);
    }

    $html .= base_consultar($tabela, $campos, $colunas, $view, $filtros);

    $html .= base_formBotaoCadastrar($view);

    return $html;
}

function base_formBegin($method) {

    $result = router_filterController();

    return "\n<form method='$method'
	action='?action={$result['action']}&view={$result['view']}'
	class='form-horizontal'>
	<fieldset>

		<!-- Form Name -->
		<legend>" . ucfirst($result['action']) . " de " . ucfirst($result['view']) . "</legend>\n";
}

function base_formEnd() {
    return "    \n</fieldset>
</form>\n";
}

/**
 * Construir um text input fornecendo um array com Name, Label, Placeholder, Type e Required.
 * @param array $info Ex: array('name','Label','Placeholder','Type')
 * @return Html Form Input
 */
function base_formTextInput(array $info) {
    return "\n<!-- Text input-->
		<div class='control-group'>
			<label class='control-label' for='{$info[0]}'>{$info[1]}:</label>
			<div class='controls'>
				<input id='{$info[0]}' name='{$info[0]}' type='{$info[3]}'
					placeholder='{$info[2]}' class='input-xlarge' {$info[4]}>
			</div>
		</div>\n";
}

/**
 * Construir um Select Basic fornecendo um array com Name, Label e array de Options.
 * @param array $info Ex: array('name','Label')
 * @param array $options Ex: array('value','option')
 * @return Html Form Input
 */
function base_formSelectBasic(array $info, array $options) {
    $opts = "";

    foreach ($options as $key => $value) {
        $opts .= "<option value='$key'>$value</option>\n";
    }

    return "\n<div class='control-group'>
			<label class='control-label' for='{$info[0]}'>{$info[1]}:</label>
			<div class='controls'>
				<select id='{$info[0]}' name='{$info[0]}' class='input-xlarge'>
                                        $opts
				</select>
			</div>
		</div>\n";
}

/**
 * Constroi um Button Submit simples
 * @param string $buttonName Nome do Botao
 * @return Html form Submit
 */
function base_formButtonSubmit($buttonName) {
    return "\n<!-- Button -->
        <div class='control-group'>
            <label class='control-label' for='singlebutton'></label>
            <div class='controls'>
                <button id='singlebutton' name='singlebutton'
                        class='btn btn-primary'>$buttonName</button>
            </div>
        </div>\n";
}

function base_alert($enfase, $msg, $tipo = '', $tamanho = '') {
    return "<div class='alert $tipo $tamanho'>
            <button type='button' class='close' data-dismiss='alert'>×</button>
            <strong>$enfase!</strong> $msg
          </div>";
}

/**
 * Retorna true ou false se houve requisição POST ou GET Seguro com filtro.
 * @param string $method EX: Post ou Get
 * @return boolean
 */
function base_requestMethod($method) {
    return filter_input(INPUT_SERVER, 'REQUEST_METHOD') == strtoupper($method);
}

function base_mensageria($option) {
    switch ($option) {
        case 001 :
            return base_alert("Erro", "Login ou Senha Inválidos.");
        case 002 :
            return base_alert("Atenção", "Sem permissão de acesso.", 'alert-error');
        case 003 :
            return base_alert("Sucesso!", "Registro Cadastrado.", 'alert-success');
        case 004 :
            return base_alert("Sucesso!", "Registro Alterado.", 'alert-success');
        case 005 :
            return base_alert("Sucesso!", "Registro Excluído.", 'alert-success');
        default :
            break;
    }
}

?>