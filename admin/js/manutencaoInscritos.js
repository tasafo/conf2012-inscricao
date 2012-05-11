$(document).ready(function($) {
    $("#mudar").click( function() {
        mudar_nome_instituicao();
    });
    
    $("#reativar").click( function() {
        reativar_cancelado();
    });
    
    $("#novo_nome").focus();
});

function mudar_nome_instituicao() {
    if ($("#novo_nome").val() == "") {
        alert("Informe o novo nome");
        $("#novo_nome").focus();
        return false;
    }
        
    jConfirm("Deseja realizar a troca do nome?", null, function(r) {
        if (r == true) {
            parametros = $('#form').serialize();
            
            $("#processando").text("Processando, aguarde...");
            
            $.ajax({
                type: "POST",
                url: "trocarNomeInstituicao.php",
                dataType: "xml",
                data: parametros,
                success: analisarRespostaTrocar
            });
            
            $().ajaxStop(function() {
                $("#processando").text("");
            });
        }
    });
}

function analisarRespostaTrocar(xml) {
    erro = $('erro', xml).text();
    
    $("#processando").text("");
    
    if (erro) {
        alert(erro);
        
        return false;
    } else {
        alert($('msg', xml).text());
        document.location.reload();
    }

    return true;
}

function reativar_cancelado() {
    jConfirm("Deseja reativar as inscrições canceladas?", null, function(r) {
        if (r == true) {
            parametros = $('#form').serialize();
            
            $("#processando").text("Processando, aguarde...");
            
            $.ajax({
                type: "POST",
                url: "reativarInscricoes.php",
                dataType: "xml",
                data: parametros,
                success: analisarRespostaReativar
            });
            
            $().ajaxStop(function() {
                $("#processando").text("");
            });
        }
    });
}

function analisarRespostaReativar(xml) {
    erro = $('erro', xml).text();
    
    $("#processando").text("");
    
    if (erro) {
        alert(erro);
        
        return false;
    } else {
        alert($('msg', xml).text());
        document.location.reload();
    }

    return true;
}