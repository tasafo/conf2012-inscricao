$(document).ready(function($){
    $("#recuperar").click( function() {
        validar_recuperar();
    });
    
    $("#email").focus();
});

function validar_recuperar() {
    $('#form_recupera').validate({
        meta: "validate",
        rules: {
            email:{
                required: true,
                email: true
            }
        },
        messages: {
            email: '<font color="red"><b>Informe o E-mail</b></font>',
        },
        submitHandler: function(form) {
            recuperar_pagamento();
        }
    });

    $('#form_recupera').submit();
}

function recuperar_pagamento() {
    parametros = $('#form_recupera').serialize();
    $.ajax({
        type: "POST",
        url: "RespRecuperaPagamento.php",
        dataType: "xml",
        data: parametros,
        success: analisarRespostaRecupera
    });
}

function analisarRespostaRecupera(xml) {
    erro = $('erro', xml).text();
    if (erro) {
        alert(erro);        
        return false;
    } else {
        id = $('id', xml).text();
        tipo = $('tipo', xml).text();
        
        window.location = 'pagamento' + tipo + '.php?id=' + id;
    }

    return true;
}
 
