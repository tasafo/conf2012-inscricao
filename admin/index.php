<?php
session_start();
require_once '../general/autoload.php';
require_once '../util/constantes.php';

unset($_SESSION['permissaoAdmin']);
unset($_SESSION['logado']);
$msg_erro = "";

$o_usuario = new UsuarioDAO();

if (!isset($_POST['email']) || !isset($_POST['senha'])) {
    $msg_erro .= "Informe o e-mail e senha do usuário.<br>";
  
} else {
    $email = $_POST['email'];
    $senha = MD5(trim($_POST['senha']));
    
    $a_usuarios = $o_usuario->busca("email = '$email' AND senha = '$senha'");
    
    if ($a_usuarios) {
        $a_usuarios = $a_usuarios[0];
        $nome_usuario = utf8_encode($a_usuarios->nome);
        $perfis_usuario = $a_usuarios->perfis;
        
        $login = explode("@", $email);
        $login = $login[0];
        
        $_SESSION['logado']['nome'] = $nome_usuario;
        $_SESSION['logado']['email'] = $email;
        $_SESSION['logado']['login'] = $login;
        
        if (strstr($perfis_usuario, "admin")) {
            $_SESSION['permissaoAdmin'] = 'ok';

            die("<script>window.location='menu.php'</script>");
        
        } elseif (strstr($perfis_usuario, "organizador")) {
            die("<script>window.location='marcar_presenca.php'</script>");
            
        }
    } else {
        $msg_erro .= "Usuário e senha não estão registrados no sistema.<br>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
    <meta charset="utf-8">
        <title>Login</title>
    </head>
    <body onload="document.permissao.email.focus();">
        <center>
            <p style="color: red"><?php echo $msg_erro ?></p>
            <br><br>
            <form name="permissao" method="post" action="index.php">
                E-mail: <input type="text" name="email" size="20"/><br><br>
                Senha: <input type="password" name="senha" size="20"/><br><br>
                <input type="submit" name="acessar" value="acessar" />
            </form>
        </center>
    </body>
</html>