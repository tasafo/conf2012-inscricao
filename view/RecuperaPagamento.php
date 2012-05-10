<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <title>Inscrição Individual</title>
        <script type="text/javascript" src="js/jquery/jquery.js"></script>
        <script type="text/javascript" src="js/jquery/jquery.validate.js"></script>
        <script type="text/javascript" src="js/recupera_pagamento.js"></script>
        <link href="css/validacao.css" rel="stylesheet" />
        <link href="css/estilo.css" rel="stylesheet" />
    </head>
    <body>
        <h3>Recupere o link do Pagseguro informando seu e-mail logo abaixo</h3>
        <form id="form_recupera" name="form_recupera" action="RespRecuperaPagamento.php" method="post">
            <input type="text" name="email" id="email" maxlength="100" size="30" />
            <input type="button" id="recuperar" name="recuperar" value="Recuperar" />
        </form> 
    </body>
</html