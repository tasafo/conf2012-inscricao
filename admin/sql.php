<?php
require 'validaSessao.php';
require_once '../general/autoload.php';

if ($_POST['sql']) {
    $sql = stripcslashes($_POST['sql']);

    $banco = new Banco();
    
    $resultado = $banco->executar($sql);
    
    $erro_sql = $banco->erro_sql;

    $banco->desconectar();

    if ($resultado != 1)
        echo $erro_sql;
    else
        echo "OK";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
        <title>SQL</title>
        <link href="css/admin.css" rel="stylesheet" />
    </head>
    <body>
        <center>
            <h3><a href="menu.php">Voltar ao Menu</a></h3>
            <h2>Manutenção</h2>
        </center>
        <form action="" method="post">
            SQL:<br>
            <textarea rows="15" cols="80" name="sql"><?php echo $sql ?></textarea><br><br>
            <input type="submit" name="submit" value="Executar">
        </form>
    </body>
</html>