$(document).ready(function($){
	// Funcoes para os links
	$("#gravar").click( function() {
		validar();
	});

    $("#permito_divulgacao").click(function() {
        $("#permito_divulgacao").val($(this).attr("checked") ? 'S' : 'N');
    });
    
	$("#nome").focus();
});

function validar(){
    var container = $('div.container');
    
	$('#form').validate({
        errorContainer: container,
		errorLabelContainer: $("ol", container),
		wrapper: 'li',
		meta: "validate",
		//  define regras para os campos
		rules: {
			nome: {
				required: true
			},
			email:{
				required: true,
				email: true
			},
            profissao: {
                required: true
            },
            instituicao: {
				required: true
			},
            cep: {
				required: true,
				digits: true,
				minlength: 8
			}
		},
		// define messages para cada campo
		messages: {
            nome: 'Informe seu Nome',
            email: 'Informe seu E-mail',
            profissao: 'Informe sua Profiss&atilde;o',
            instituicao: 'Informe sua Institui&ccedil;&atilde;o',
            cep: 'Informe seu CEP'
		},
		submitHandler: function(form) {
			salvar();
		}
	});

	$('#form').submit();
}

function salvar() {
	//$("#div_botao_salvar").hide("fast");

	$("#div_salvando").show("fast",function() {
		$(this).html("<b>Salvando. Aguarde um momento...</b>");
	});

	parametros = $('#form').serialize();
	$.ajax({
		type: "POST",
		url: "RespCadastrarInscricaoIndividual.php",
		dataType: "xml",
		data: parametros,
		success: analisarResposta
	});

    $().ajaxStop(function(){
		$("#div_salvando").fadeOut("slow",function(){
			$(this).html("");
			//$("#div_botao_salvar").fadeIn("slow");
		});
	});
}

function analisarResposta(xml) {
    erro = $('erro', xml).text();
    if (erro) {
        alert(erro);

		$("#div_salvando").fadeOut("slow",function(){
			$(this).html("");
			//$("#div_botao_salvar").fadeIn("slow");
		});
        
        return false;
    } else {
        idIndividual = $('id', xml).text();

        alert($('msg', xml).text());
        window.location = 'pagamentoIndividual.php?id=' + idIndividual;    
    }

    return true;
}
