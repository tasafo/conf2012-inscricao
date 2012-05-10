$(document).ready(function($) {
	$("#insere_funcionario").click( function() {
		validar_funcionario();
	});

	$("#func_nome").focus();
});

function validar_funcionario() {
    var container = $('#div_msg_funcionario');

	$('#frmFunc').validate({
        errorContainer: container,
		errorLabelContainer: $("ol", container),
		wrapper: 'li',
		meta: "validate",
		//  define regras para os campos
		rules: {
			func_nome: {
				required: true
			},
			func_email:{
				required: true,
				email: true
			},
            func_profissao: {
                required: true
            }
		},
		// define messages para cada campo
		messages: {
            func_nome: 'Informe o nome do funcionario',
            func_email: 'Informe um E-mail valido do funcionario'
		},
		submitHandler: function(form) {
			salvar();
		}
	});

    $('#frmFunc').submit();
}

function salvar() {
	$("#div_salvando").show("fast",function() {
		$(this).html("<font color='red'><b>Salvando. Aguarde um momento...</b></font>");
	});

	parametros = $('#frmFunc').serialize();
	$.ajax({
		type: "POST",
		url: "RespAddEmployee.php",
		data: parametros,
		success: analisarResposta
	});

    $().ajaxStop(function(){
		$("#div_salvando").fadeOut("slow",function(){
			$(this).html("");
		});
	});
}

function analisarResposta(txt) {
    mensagem = txt;

    if (mensagem.toString().substr(0, 7) == 'Atencao') {
        alert(mensagem);

        return false;
    } else {
        $('#div_grade_funcionarios').html(txt);
        $('#div_grade_funcionarios').show();

        $('#frmFunc')[0].reset();
        $('#func_nome').focus();
    }

    return true;
}

function confirmaCancelamento(idIndividual) {
    jConfirm("Deseja realizar o cancelamento da inscricao?", null, function(r) {
        if (r == true) {
            parametros = 'idIndividual=' + idIndividual;

            $.ajax({
                type: "POST",
                url: "confirmaCancelamentoIndividualAjax.php",
                dataType: "xml",
                data: parametros,
                success: analisarRespostaCancelamentoFuncionario
            });
        }
    });
}

function analisarRespostaCancelamentoFuncionario(xml) {
    erro = $('erro', xml).text();
    idIndividual = $('idIndividual', xml).text();

    if (erro) {
        alert(erro);

        return false;
    } else {
        mensagem = $('mensagem', xml).text();

        $("#row_" + idIndividual).remove();

        alert(mensagem);
    }

    return true;
}