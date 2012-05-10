<?php
require 'validaSessao.php';
require_once '../general/autoload.php';

$ordem = "nome";
if ($_GET['ordem'])
    $ordem = $_GET['ordem'];

$o_inscricao = new InscricaoDAO();

$a_relacao_inscritos = $o_inscricao->selecionar_relacao_geral_inscritos($ordem);

if (!$a_relacao_inscritos) {
	echo '<center><h3><a href="menu.php">Voltar ao Menu</a></h3><br>';
	die("<h2>Nenhuma inscri&ccedil;&atilde;o encontrada</h2></center>");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
        <title>Rela&ccedil;&atilde;o Geral de Inscritos</title>
        <style type="text/css" title="mystyles" media="all">
            table.bordasimples {border-collapse: collapse;}

            table.bordasimples tr td {border:1px solid #000000;}
        </style>
    </head>
    <body>
        <center><a href="menu.php">Voltar ao Menu</a></center>
        <br>
        <table width="100%" border="1" class="bordasimples">
            <tr style="font-weight: bold; text-align: center">
                <td colspan="6">Relação Geral de Inscritos</td>
            </tr>
            <tr style="font-weight: bold; text-align: center">
                <td width="5%" align="center">Ordem</td>
                <td width="5%" align="center">Inscrição</td>
                <td width="30%">Nome</td>
                <td width="20%">Instituição</td>
                <td width="20%">Categoria</td>
                <td width="20%">Assinatura</td>
            </tr>
            <?php
            $ordem = 1;
            foreach ($a_relacao_inscritos as $inscrito) {
                $id = $inscrito->id;
                $nome = $inscrito->nome;
                $instituicao = $inscrito->instituicao;
                $categoria = $inscrito->descricao_tipo_inscricao;
            ?>
            <tr>
                <td align="center"><?php echo $ordem++ ?></td>
                <td align="center"><?php echo $id ?></td>
                <td><?php echo utf8_encode($nome) ?></td>
                <td><?php echo utf8_encode($instituicao) ?></td>
                <td><?php echo $categoria ?></td>
                <td>&nbsp;</td>
            </tr>
            <?php
            }
            ?>
        </table>
    </body>
</html>