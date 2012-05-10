$(document).ready(function($) {

});

function marcaCortesia(idInscricao) {
    if ($("#cortesia_" + idInscricao + ":checked").val()) {
        $("#cortesia_" + idInscricao).val('S');
    } else {
        $("#cortesia_" + idInscricao).val('N');
    }
}

function confirmaPagamento(idInscricao) {
    jConfirm("Deseja realizar o pagamento?", null, function(r) {
        if (r == true) {
            dtPagamento = $("#data_" + idInscricao).val();
            dtCompensacao = $("#compensacao_" + idInscricao).val();
            txPagamento = $("#taxa_" + idInscricao).val();
            nome = $("#nome_" + idInscricao).text();
            email = $("#email_" + idInscricao).text();
            cortesia = $("#cortesia_" + idInscricao).val();

            parametros = 'dtPagamento=' + dtPagamento +
                '&dtCompensacao=' + dtCompensacao +
                '&txPagamento=' + txPagamento +
                '&nome=' + nome +
                '&email=' + email +
                '&cortesia=' + cortesia +
                '&idInscricao=' + idInscricao;

            $("#div_botao_" + idInscricao).hide();
            $("#gravando_" + idInscricao).text("Processando...");

            $.ajax({
                type: "POST",
                url: "confirmaPagamentoIndividualAjax.php",
                dataType: "xml",
                data: parametros,
                success: analisarRespostaPagamentoIndividual
            });

            $().ajaxStop(function() {
                $("#gravando_" + idInscricao).text("");
            });
        }
    });
}

function analisarRespostaPagamentoIndividual(xml) {
    erro = $('erro', xml).text();
    idInscricao = $('idInscricao', xml).text();

    $("#gravando_" + idInscricao).text("");

    if (erro) {
        $("#div_botao_" + idInscricao).show();
        
        alert(erro);
        
        return false;
    } else {
        dataPagamento = $('dataPagamento', xml).text();
        dataCompensacao = $('dataCompensacao', xml).text();
        mensagem = $('mensagem', xml).text();

        $('#div_data_pagamento_' + idInscricao).html(dataPagamento);
        $('#div_data_compensacao_' + idInscricao).html(dataCompensacao);
        $('#div_taxa_pagamento_' + idInscricao).html("&nbsp;");
        $('#div_botao_' + idInscricao).html("&nbsp;");
        $('#div_cortesia_' + idInscricao).html("&nbsp;");
        $('#div_cancelar_' + idInscricao).html("&nbsp;");

        alert(mensagem);
    }

    return true;
}

function confirmaCancelamento(idIndividual) {
    jConfirm("Deseja realizar o cancelamento da inscricao?", null, function(r) {
        if (r == true) {
            parametros = 'idIndividual=' + idIndividual;

            $("#div_cancelar_" + idIndividual).hide();
            $("#cancelando_" + idIndividual).text("Processando...");

            $.ajax({
                type: "POST",
                url: "confirmaCancelamentoIndividualAjax.php",
                dataType: "xml",
                data: parametros,
                success: analisarRespostaCancelamentoIndividual
            });

            $().ajaxStop(function() {
                $("#cancelando_" + idIndividual).text("");
            });
        }
    });
}

function analisarRespostaCancelamentoIndividual(xml) {
    erro = $('erro', xml).text();
    idIndividual = $('idIndividual', xml).text();

    $("#cancelando_" + idIndividual).text("");

    if (erro) {
        $("#div_cancelar_" + idIndividual).show();

        alert(erro);

        return false;
    } else {
        mensagem = $('mensagem', xml).text();

        $("#row_" + idIndividual).remove();

        alert(mensagem);
    }

    return true;
}
