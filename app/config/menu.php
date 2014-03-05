
<?php $result = router_filterController(); ?>

	<div class="navbar">
	  <div class="navbar-inner">
	    <a class="brand" href="?">Sistema</a>
	    <ul class="nav">
              <li <?php if ($result['view'] == 'tarefas') {echo "class='active'";} ?> ><a href="?view=tarefas">Tarefas</a></li>
	      <li <?php if ($result['view'] == 'usuarios') {echo "class='active'";} ?> ><a href="?view=usuarios">Usuários</a></li>
	      <li <?php if ($result['view'] == 'funcionarios') {echo "class='active'";} ?> ><a href="?view=funcionarios">Funcionários</a></li>
              <li><a href="?action=logout">Sair</a></li>
	    </ul>
	  </div>
	</div>

<?php unset($result) ?>