function excluir(link) {
	
	rs = confirm("Você deseja excluir este registro?");
	
	if (rs)
	{
		location.assign(link);	
	}	
};