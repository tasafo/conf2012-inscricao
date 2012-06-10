<?php
require 'validaSessao.php';
require_once '../general/autoload.php';

$o_inscricao = new InscricaoDAO();

$a_presentes = $o_inscricao->lista_de_presentes();

if (!$a_presentes) {
    die("<h2>Nenhuma inscri&ccedil;&atilde;o encontrada</h2></center>");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <title>Relatório de Presentes</title>
        <link href="css/admin.css" rel="stylesheet" />
    </head>
    <body>
        <h2><center>Relatório de Presentes</center></h2>
        <table width="100%" border="1" class="bordasimples">
            <tr style="font-weight: bold">
                <td>Nome</td>
                <td>E-mail</td>
                <td>Instituição</td>
            </tr>
            <?php foreach ($a_presentes as $presente) { ?>
            <tr>
                <td><?php echo utf8_encode($presente->nome) ?></td>
                <td><?php echo $presente->email ?></td>
                <td><?php echo utf8_encode($presente->instituicao) ?></td>
            </tr>
            <?php } ?>
            <tr style="font-weight: bold;">
                <td colspan="4"><?php echo count($a_presentes) ?> inscrito(s) presente(s)</td>
            </tr>
        </table>
    </body>
</html>