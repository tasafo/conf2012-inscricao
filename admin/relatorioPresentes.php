<?php
require 'validaSessao.php';
require_once '../general/autoload.php';

$o_inscricao = new InscricaoDAO();

$a_inscritos_individual = $o_inscricao->selecionar_inscritos_individual(false, "ind.nome");

if (!$a_inscritos_individual) {
    echo '<center><h3><a href="menu.php">Voltar ao Menu</a></h3><br>';
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
        <h3><center><a href="menu.php">Voltar ao Menu</a></center></h3>
        <h2><center>Relatório de Presentes</center></h2>
        <table width="100%" border="1" class="bordasimples">
            <tr style="font-weight: bold">
                <td>Nome</td>
                <td>E-mail</td>
                <td>Categoria</td>
                <td>Instituição</td>
            </tr>
            <?php
            $contador = 0;
            foreach ($a_inscritos_individual as $individual) {
                if ($individual->presente == "S") {
                    $contador++;
            ?>
            <tr>
                <td><?php echo utf8_encode($individual->nome) ?></td>
                <td><?php echo $individual->email ?></td>
                <td><?php echo $individual->descricao_tipo_inscricao ?></td>
                <td><?php echo utf8_encode($individual->instituicao) ?></td>
            </tr>
            <?php
                }
            }
            ?>
            <tr style="font-weight: bold;">
                <td colspan="4"><?php echo $contador ?> inscrito(s) presente(s)</td>
            </tr>
        </table>
    </body>
</html>