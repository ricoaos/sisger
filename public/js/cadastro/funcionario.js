$(document).ready(function(){
	
	$("#num_cep").mask('99.999-999');
    $("#id_cliente,#id_ativo,#num_logradouro,#ds_complemento,#ds_logradouro,#ds_bairro,#ds_cidade,#ds_uf").attr('readonly',true).css({'background':'#F0F0F0'});
    $("#num_cpf").mask("999.999.999-99");
    $("#dt_nascimento").mask("99/99/9999");

    $("#num_cpf").change(function() {
				
        if(!ValidarCPF($(this).val())){
            alert('CPF inválido!');
            $(this).val('').focus();
            return;
        };
    });
   
	$("#num_cep").change(function(){
        var cep = $(this).val().replace(/\D/g,"");
        if($(this).val() != ''){
            $.ajax({
                url : 'https://viacep.com.br/ws/'+cep+'/json/',
                dataType: 'json',
                success: function(response){
                    var objResult = response;
                    if(objResult.resultado != 0){
                        $("#num_logradouro,#ds_complemento,#ds_logradouro").attr('readonly',false).css({'background':'#FFF'});
                        $('#ds_logradouro').val(objResult.logradouro);
                        $('#ds_bairro').val(objResult.bairro);
                        $('#ds_cidade').val(objResult.localidade);
                        $('#ds_uf').val(objResult.uf);
                        $("#ds_municipio").val(objResult.ibge);
                        $("#num_logradouro").focus();
                    }else{
                        alert(objResult.resultado_txt);
                        $("#num_cep").val('').focus();
                    }
                },
                error: function (request, status, erro) {
                    alert(request.responseText);
                    alert("Problema ocorrido: " + status + "\nDescição: " + erro);
                    alert("Informações da requisição: \n" + request.getAllResponseHeaders());
                }
            });
        }
    });
	
	$("#num_cep").click(function(){
        $("#num_logradouro,#ds_complemento,#ds_logradouro,#ds_bairro,#ds_cidade,#ds_uf").attr('readonly',true).css({'background':'#F0F0F0'}).val('');
        $("#num_cep").val('');
    });
	 
});
