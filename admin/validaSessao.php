<?php
session_start();

if (!$_SESSION['permissaoAdmin'] || $_SESSION['permissaoAdmin'] != 'ok' ) {
?>
    <script>
        alert('Acesso nao permitido! Faca o login no sistema.');
        window.location='index.php';
    </script>
<?php
} else {
    echo "<b style='color: blue'>Usuário:</b> " . $_SESSION['logado']['nome'] . "<br><br>";
}
?>