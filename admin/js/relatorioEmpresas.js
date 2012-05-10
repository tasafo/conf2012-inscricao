$(document).ready(function($){

});

function marcaCortesia(idEmpresa) {
    if ($("#cortesia_" + idEmpresa + ":checked").val()) {
        $("#cortesia_" + idEmpresa).val('S');
    } else {
        $("#cortesia_" + idEmpresa).val('N');
    }
}

function confirmaPagamento(idEmpresa) {
    jConfirm("Deseja realizar o pagamento?", null, function(r) {
        if (r == true) {
            dtPagamento = $("#data_" + idEmpresa).val();
            dtCompensacao = $("#compensacao_" + idEmpresa).val();
            txPagamento = $("#taxa_" + idEmpresa).val();
            nome = $("#nome_" + idEmpresa).text();
            email = $("#email_" + idEmpresa).text();
            cortesia = $("#cortesia_" + idEmpresa).val();

            parametros = 'dtPagamento=' + dtPagamento +
                '&dtCompensacao=' + dtCompensacao +
                '&txPagamento=' + txPagamento +
                '&nome=' + nome +
                '&email=' + email +
                '&cortesia=' + cortesia +
                '&idEmpresa=' + idEmpresa;

            $("#salvando_" + idEmpresa).text("Processando, aguarde...");

            $.ajax({
                type: "POST",
                url: "confirmaPagamentoEmpresaAjax.php",
                dataType: "xml",
                data: parametros,
                success: analisarRespostaPagamentoEmpresa
            });

            $().ajaxStop(function() {
                $("#salvando_" + idEmpresa).text("");
            });
        }
    });
}

function analisarRespostaPagamentoEmpresa(xml) {
    erro = $('erro', xml).text();
    idEmpresa = $('idEmpresa', xml).text();

    $("#salvando_" + idEmpresa).text("");

    if (erro) {
        alert(erro);

        return false;
    } else {
        dataPagamento = $('dataPagamento', xml).text();
        dataCompensacao = $('dataCompensacao', xml).text();
        mensagem = $('mensagem', xml).text();

        $('#div_data_pagamento_' + idEmpresa).html(dataPagamento);
        $('#div_data_compensacao_' + idEmpresa).html(dataCompensacao);
        $('#div_taxa_pagamento_' + idEmpresa).html("&nbsp;");
        $('#div_botao_' + idEmpresa).html("&nbsp;");
        $('#div_cortesia_' + idEmpresa).html("&nbsp;");

        alert(mensagem);
    }

    return true;
}
