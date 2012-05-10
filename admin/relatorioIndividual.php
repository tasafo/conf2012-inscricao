<?php
require 'validaSessao.php';
require_once '../general/autoload.php';
require_once '../util/constantes.php';

$o_inscricao = new InscricaoDAO();

$a_inscritos_individual = $o_inscricao->selecionar_inscritos_individual();

if (!$a_inscritos_individual) {
	echo '<center><h3><a href="menu.php">Voltar ao Menu</a></h3><br>';
	die("<h2>Nenhuma inscri&ccedil;&atilde;o encontrada</h2></center>");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
        <title>Inscritos Individualmente</title>
        <script type="text/javascript" src="../view/js/jquery/jquery.js" ></script>
        <script type="text/javascript" src="../view/js/jquery/jquery.alerts/jquery.alerts.js" ></script>
        <script type="text/javascript" src="../view/js/validacao.js" ></script>
        <script type="text/javascript" src="js/relatorioIndividual.js" ></script>
        <link href="../view/js/jquery/jquery.alerts/jquery.alerts.css" rel="stylesheet" />
        <link href="css/admin.css" rel="stylesheet" />
    </head>
    <body>
        <h3><center><a href="menu.php">Voltar ao Menu</a></center></h3>
        <h2><center>Inscritos Individualmente</center></h2>
        <table width="100%" border="1" class="bordasimples">
            <tr style="font-weight: bold">
                <td align="center">Id Insc.</td>
                <td align="center">Data Insc.</td>
                <td>Id - Nome | E-mail | Instituição</td>
                <td>Tipo Insc.</td>
                <td align="right">(+)Valor</td>
                <td align="right">(-)Taxa</td>
                <td align="right">(=)Total</td>
                <td align="center" width="20%">Pagto. | Compens.</td>
                <td>Cortesia?</td>
                <td align="center">Operações</td>
            </tr>
            <?php
            $contador = 0;
            $contadorConfirmados = 0;
            $contadorEmAberto = 0;
            $valorInscricaoConfirmados = 0;
            $valorInscricaoEmAberto = 0;
            $valorInscricao = 0;
            $valorTaxaInscricao = 0;
            $valorTotalInscricao = 0;
            
            foreach ($a_inscritos_individual as $individual) {
                $contador++;
                $idIndividual = $individual->id_individual;
                $idInscricao = $individual->id_inscricao;
                $nome = utf8_encode($individual->nome);
                $valorInscricao += $individual->valor;
                $valorTaxaInscricao += $individual->taxa;
                $subTotalInscricao = $individual->valor - $individual->taxa;

                if (empty($individual->data_pagamento)) {
                    $cor = "red";
                    $contadorEmAberto++;
                    $valorInscricaoEmAberto += $individual->valor;

                    $dataPagamento = "Dt. Pagto.: <input type='text' size=10 maxlength=10 name='dtPagamento' id='data_$idInscricao' onkeypress='mascara(this,data);' onblur='validaData(this);' />";
                    
                    $dataCompensacao = "Dt. Compens.: <input type='text' size=10 maxlength=10 name='dtCompensacao' id='compensacao_$idInscricao' onkeypress='mascara(this,data);' onblur='validaData(this);' />";
                    
                    $taxaPagamento = "Taxa: <input type='text' size=10 maxlength=10 name='taxaPagamento' id='taxa_$idInscricao' onKeyUp='this.value = soValorC(this.value, 2)' style='text-align: right' />";
                    
                    $cortesia = "<input type='checkbox' name='cortesia' id='cortesia_$idInscricao' value='N' onclick='marcaCortesia($idInscricao)' />";
                    
                    $confirmar = "<input type='button' name='confirmar' id='confirmar' value='Pagar' onclick='confirmaPagamento($idInscricao)' />";
                    
                    $cancelar = "<input type='button' name='cancelar' id='cancelar' value='Cancelar' onclick='confirmaCancelamento($idIndividual)' />";
                } else {
                    $cor = "blue";
                    $contadorConfirmados++;
                    $valorInscricaoConfirmados += $individual->valor;
                    $valorTotalInscricao += $subTotalInscricao;

                    $dataPagamento = Funcoes::formata_data_para_exibir($individual->data_pagamento);
                    $dataCompensacao = empty($individual->data_compensacao) ? "" : Funcoes::formata_data_para_exibir($individual->data_compensacao);
                    $taxaPagamento = "";
                    $cortesia = "&nbsp;";
                    $confirmar = "&nbsp;";
                    $cancelar = "&nbsp;";
                }
            ?>
            <tr id="row_<?php echo $idIndividual ?>" style="color: <?php echo $cor ?>;">
                <td align="center"><?php echo $idInscricao ?></td>
                <td align="center"><?php echo Funcoes::formata_data_para_exibir($individual->data_registro) ?></td>
                <td>
                    <?php echo $idIndividual ?> - 
                    <span id="nome_<?php echo $idInscricao ?>"><?php echo $nome ?></span><br/>
                    <span id="email_<?php echo $idInscricao ?>"><?php echo $individual->email ?></span><br/>
                    <?php echo utf8_encode($individual->instituicao) ?>
                </td>
                <td><?php echo $individual->descricao_tipo_inscricao ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($individual->valor) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($individual->taxa) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($subTotalInscricao) ?></td>
                <td align="right">
                    <div id="div_data_pagamento_<?php echo $idInscricao ?>"><?php echo $dataPagamento ?></div>
                    <div id="div_data_compensacao_<?php echo $idInscricao ?>"><?php echo $dataCompensacao ?></div>
                    <div id="div_taxa_pagamento_<?php echo $idInscricao ?>"><?php echo $taxaPagamento ?></div>
                </td>
                <td align="center">
                    <div id="div_cortesia_<?php echo $idInscricao ?>"><?php echo $cortesia ?></div>
                </td>
                <td align="center">
                    <div id="div_botao_<?php echo $idInscricao ?>"><?php echo $confirmar ?></div>
                    <span style="color: red" id="gravando_<?php echo $idInscricao ?>"></span><br/>
                    <div id="div_cancelar_<?php echo $idIndividual ?>"><?php echo $cancelar ?></div>
                    <span style="color: red" id="cancelando_<?php echo $idIndividual ?>"></span>
                </td>
            </tr>
            <?php
            }
            ?>
            <tr style="font-weight: bold; color: red">
                <td colspan="4">Valor total de [ <?php echo $contadorEmAberto ?> ] inscrito(s) em aberto</td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorInscricaoEmAberto) ?></td>
                <td colspan="5">&nbsp;</td>
            </tr>
            <tr style="font-weight: bold; color: blue">
                <td colspan="4">Valor total de [ <?php echo $contadorConfirmados ?> ] inscrito(s) confirmado(s)</td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorInscricaoConfirmados) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorTaxaInscricao) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorTotalInscricao) ?></td>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr style="font-weight: bold; color: green">
                <td colspan="4">Valor total de [ <?php echo $contador ?> ] inscrito(s)</td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorInscricao) ?></td>
                <td colspan="5">&nbsp;</td>
            </tr>
        </table>
    </body>
</html>