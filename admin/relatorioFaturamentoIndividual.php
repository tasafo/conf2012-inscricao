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
        <title>Faturamento de Inscrições Individuais</title>
        <link href="css/admin.css" rel="stylesheet" />
    </head>
    <body>
        <h3><center><a href="menu.php">Voltar ao Menu</a></center></h3>
        <h2><center>Faturamento de Inscrições Individuais</center></h2>
        <table width="100%" border="1" class="bordasimples">
            <tr style="font-weight: bold">
                <td>Nome</td>
                <td>Tipo Insc.</td>
                <td align="right">(+)Valor</td>
                <td align="right">(-)Taxa</td>
                <td align="right">(=)Total</td>
            </tr>
            <?php
            $contador = 0;
            $contadorConfirmados = 0;
            $valorInscricaoConfirmados = 0;
            $valorInscricao = 0;
            $valorTaxaInscricao = 0;
            $valorTotalInscricao = 0;
            
            foreach ($a_inscritos_individual as $individual) {
                $contador++;
                $nome = utf8_encode($individual->nome);
                $valorInscricao += $individual->valor;
                $valorTaxaInscricao += $individual->taxa;
                $subTotalInscricao = $individual->valor - $individual->taxa;

                if (!empty($individual->data_pagamento)) {
                    $contadorConfirmados++;
                    $valorInscricaoConfirmados += $individual->valor;
                    $valorTotalInscricao += $subTotalInscricao;
            ?>
            <tr id="row_<?php echo $idIndividual ?>">
                <td><?php echo $nome ?></td>
                <td><?php echo $individual->descricao_tipo_inscricao ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($individual->valor) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($individual->taxa) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($subTotalInscricao) ?></td>
            </tr>
            <?php
                }
            }
            ?>
            <tr style="font-weight: bold;">
                <td colspan="2">Valor total de <?php echo $contadorConfirmados ?> inscrito(s) confirmado(s)</td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorInscricaoConfirmados) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorTaxaInscricao) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorTotalInscricao) ?></td>
            </tr>
        </table>
    </body>
</html> 
