$(document).ready(function($) {
    $("#presenca").click( function() {
        marcar_presenca();
    });
});

function marcar_presenca() {
    jConfirm("Deseja marcar a presença dos inscritos?", null, function(r) {
        if (r == true) {
            parametros = $('#form').serialize();
            
            $("#processando").text("Processando, aguarde...");
            
            $.ajax({
                type: "POST",
                url: "marcarPresenca.php",
                dataType: "xml",
                data: parametros,
                success: analisarRespostaMarcarPresenca
            });
            
            $().ajaxStop(function() {
                $("#processando").text("");
            });
        }
    });
}

function analisarRespostaMarcarPresenca(xml) {
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