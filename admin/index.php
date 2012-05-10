<?php
session_start();

require_once '../util/constantes.php';
?>
<html>
    <head>
        <title>Login</title>
    </head>
    <body onload="document.permissao.usuario.focus();">
    <?php
    if ( !$_POST['senha'] || $_POST['senha'] != SENHA_ADMIN  &&
       !$_POST['usuario'] || $_POST['usuario'] != USUARIO_ADMIN ) {
        unset($_SESSION['permissaoAdmin']);
    ?>
        <center>
            <br><br>
            <form name="permissao" method="post" action="index.php">
            	Usu&aacute;rio: <input type="text" name="usuario" size="20"/><br><br>
                Senha: <input type="password" name="senha" size="20"/><br><br>
                <input type="submit" name="acessar" value="acessar" />
            </form>
        </center>
    <?php
    } else {
        $_SESSION['permissaoAdmin'] = 'ok';
    ?>
        <script>window.location='menu.php'</script>
    <?php
    }
    ?>
    </body>
</html>