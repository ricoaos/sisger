$(document).ready(function(){
	
	$('#grid_roteiro').hide();
	
	$('#id_cliente').change(function(){
				
		$('#grid_roteiro').hide();
		
		$.ajax({
			url: baseUrl+'/roteiro/painel/getroteirobycliente',
			data: {id_cliente:$(this).val()},
			type:"POST",
			datatype: "json",
				
			success: function(result){
				
				var obj = JSON.parse(result); 
				var objRoteiro = obj.resultado;
				var campo = '';
				
				$.each(objRoteiro, function(i, roteiro){
					campo += '<a href='+baseUrl+'/roteiro/'+roteiro.st_sigla+'>';
					campo += '<button class="facebook">'+roteiro.st_sigla+'</button>';
					campo += '</a>';
					campo += '<span>&nbsp;&nbsp;&nbsp;&nbsp</span>'
				});
				
				if(objRoteiro != '' ){
					$('#grid_roteiro').show();
				}
				
				$("#retorno").html(campo);
			},
			error: function(jqXHR,textStatus,errorThrown){
	             var error = $.parseJSON(jqXHR.responseText);
	             var content = error.content;
	             console.log(content.message);
	             if(content.display_exceptions)
	             console.log(content.exception.xdebug_message);
			}
		});		
	});
});