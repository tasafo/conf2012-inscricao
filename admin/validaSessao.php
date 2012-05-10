<?php
session_start();

if (!$_SESSION['permissaoAdmin'] || $_SESSION['permissaoAdmin'] != 'ok' ) {
?>
    <script>
        alert('Acesso nao permitido! Faca o login no sistema.');
        window.location='index.php';
    </script>
<?php
}
?>
