$(document).ready(function(){
	
	$('#user').hide();
	
	$('#tp_usuario').change(function(){
		if($(this).val() == 'I'){
			
			$('#user').hide();
			
			$.ajax({
				url: baseUrl+'/cadastro/funcionario/getfuncionarios',
				datatype: "json",
					
				success: function(result){
					
					var obj = JSON.parse(result); 
					var objRoteiro = obj.resultado;
					var campo = '';
										
					campo += '<select name="id_funcionario" id="id_funcionario">';
					campo += '<option></option>';
					$.each(objRoteiro, function(i, funcionario){
						campo += '<option value='+funcionario.id_funcionario+'>'+funcionario.st_nome+'</option>';
					});
					campo += '</select>';
					
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
			
		}else{
			
			alert 'passei';
			
			$('#user').hide();
			
			$.ajax({
				url: baseUrl+'/cadastro/cliente/getclientes',
				datatype: "json",
					
				success: function(result){
					
					var obj = JSON.parse(result); 
					var objRoteiro = obj.resultado;
					var campo = '';
										
					campo += '<select name="id_cliente" id="id_cliente">';
					campo += '<option></option>';
					$.each(objRoteiro, function(i, valor){
						campo += '<option value='+valor.id_cliente+'>'+valor.st_nome+'</option>';
					});
					campo += '</select>';
					
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
		}
		
		$('#user').show();
	});
});