<?php
session_start();

if (!$_SESSION['logado']) {
?>
    <script>
        alert('Acesso nao permitido! Faca o login no sistema.');
        window.location='index.php';
    </script>
<?php
} else {
?>
<table width="100%">
    <tr>
        <td width="33%"><?php echo "<b style='color: blue'>Usuário:</b> " . $_SESSION['logado']['nome'] . " - " . $_SESSION['logado']['perfil'] ?></td>
        <td width="33%" align="center"><a href="menu.php">Menu Principal</a></td>
        <td width="33%" align="right"><a href="index.php">Sair</a></td>
    </tr>
</table>
<hr>
<?php
}
?>