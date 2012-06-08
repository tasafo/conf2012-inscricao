<?php
require 'validaSessao.php';
require_once '../general/autoload.php';
require_once '../util/constantes.php';

$o_inscricao = new InscricaoDAO();

$a_inscritos_empresas = $o_inscricao->selecionar_inscritos_empresas();

if (!$a_inscritos_empresas) {
	die("<h2>Nenhuma inscri&ccedil;&atilde;o encontrada</h2></center>");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
        <title>Inscritos por Instituição</title>
        <script type="text/javascript" src="../view/js/jquery/jquery.js" ></script>
        <script type="text/javascript" src="../view/js/jquery/jquery.alerts/jquery.alerts.js" ></script>
        <script type="text/javascript" src="../view/js/validacao.js" ></script>
        <script type="text/javascript" src="js/relatorioEmpresas.js" ></script>
        <link type="text/css" href="../view/js/jquery/jquery.alerts/jquery.alerts.css" rel="stylesheet" />
        <link href="css/admin.css" rel="stylesheet" />
    </head>
    <body>
        <h2><center>Inscritos por Instituição</center></h2>
        <table width="100%" border="1" class="bordasimples">
            <tr style="font-weight: bold">
                <td align="center">Id Insc.</td>
                <td align="center">Data Insc.</td>
                <td width="30%">Id - Nome | Profissão | E-mail</td>
                <td>Tipo Insc.</td>
                <td align="right">(+)Valor</td>
                <td align="right">(-)Taxa</td>
                <td align="right">(=)Total</td>
                <td width="25%" align="center">Pagamento:<br> Transação | Confirmação | Compensação | Quem registrou</td>
                <td>Cortesia?</td>
                <td align="center">Operações</td>
            </tr>
            <?php
            $contador = 0;
            $contadorEmpresa = 0;
            $contadorConfirmados = 0;
            $contadorEmAberto = 0;
            
            $idEmpresa = 0;

            $valorInscricaoEmpresa = 0;
            
            $valorInscricaoConfirmados = 0;
            $valorTaxaInscricaoConfirmados = 0;
            $valorTotalInscricaoConfirmados = 0;
            
            $valorInscricaoEmAberto = 0;
            $valorInscricao = 0;
            $valorTaxaInscricao = 0;
            
            foreach ($a_inscritos_empresas as $inscricao) {
                $contador++;
                $idInscricao = $inscricao->id_inscricao;

                if ($idEmpresa != $inscricao->id_empresa) {
                    if ($idEmpresa != 0) { // Imprimir o valor total de inscricoes da empresa

            ?>
            <tr style="font-weight: bold">
                <td colspan="4">[ <?php echo $contadorEmpresa ?> ] inscrito(s)</td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorInscricaoEmpresa) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorTaxaEmpresa) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorSubtotalEmpresa) ?></td>
                <td colspan="3"></td>
            </tr>
            <?php
                    }
                    
                    $idEmpresa = $inscricao->id_empresa;
                    $valorInscricaoEmpresa = 0;
                    $valorTaxaEmpresa = 0;
                    $valorSubtotalEmpresa = 0;
                    $contadorEmpresa = 0;

                    if (empty($inscricao->data_pagamento)) {
                        $cor = "red";
                        //$quemRegistrou = "";
                        
                        $dataTransacao = "";
                        
                        $dataPagamento = "Dt. Pagto.: <input type='text' size=10 maxlength=10 name='dtPagamento' id='data_$idEmpresa' onkeypress='mascara(this,data);' onblur='validaData(this);' />";
                        
                        $dataCompensacao = "Dt. Comp.: <input type='text' size=10 maxlength=10 name='dtCompensacao' id='compensacao_$idEmpresa' onkeypress='mascara(this,data);' onblur='validaData(this);' />";
                        
                        $taxaPagamento = "Taxa: <input type='text' size=10 maxlength=10 name='taxaPagamento' id='taxa_$idEmpresa' onKeyUp='this.value = soValorC(this.value, 2)' style='text-align: right' />";
                        
                        $confirmar = "<input type='button' name='confirmar' id='confirmar' value='Pagar' onclick='confirmaPagamento($idEmpresa)' />";
                        
                        $cortesia = "<input type='checkbox' name='cortesia' id='cortesia_$idEmpresa' value='N' onclick='marcaCortesia($idEmpresa)' />";
                    } else {
                        $cor = ($inscricao->descricao_tipo_inscricao == "Cortesia" ? "maroon" : "blue");
                        //$quemRegistrou = (empty($inscricao->quem_registrou) ? "sistema" : $inscricao->quem_registrou);
                        $dataTransacao = Funcoes::formata_data_para_exibir($inscricao->data_criacao_transacao, true);
                        $dataPagamento = Funcoes::formata_data_para_exibir($inscricao->data_pagamento, true);
                        $dataCompensacao = empty($inscricao->data_compensacao) ? "" : Funcoes::formata_data_para_exibir($inscricao->data_compensacao, true);
                        $taxaPagamento = "";
                        $confirmar = "&nbsp;";
                        $cortesia = "&nbsp;";
                    }
            ?>
            <tr style="font-weight: bold; color: <?php echo $cor ?>">
                <td colspan="2"></td>
                <td>
                    Instituição: <?php echo $idEmpresa ?> - <span id="nome_<?php echo $idEmpresa ?>"><?php echo utf8_encode($inscricao->nome_empresa) ?></span><br>
                    <span id="email_<?php echo $idEmpresa ?>">E-mail: <?php echo $inscricao->email_empresa ?></span><br>
                    Responsável: <?php echo utf8_encode($inscricao->responsavel) ?>
                </td>
                <td colspan="4"><span style="color: red" id="salvando_<?php echo $idEmpresa ?>"></span></td>
                <td align="right">
                    <div id="div_data_transacao_<?php echo $idEmpresa ?>"><?php echo $dataTransacao ?></div>
                    <div id="div_data_pagamento_<?php echo $idEmpresa ?>"><?php echo $dataPagamento ?></div>
                    <div id="div_data_compensacao_<?php echo $idEmpresa ?>"><?php echo $dataCompensacao ?></div>
                    <div id="div_taxa_pagamento_<?php echo $idEmpresa ?>"><?php echo $taxaPagamento ?></div>
                    <div id="div_quem_registou_<?php echo $idEmpresa ?>"><?php echo $quemRegistrou ?></div>
                </td>
                <td align="center">
                    <div id="div_cortesia_<?php echo $idEmpresa ?>"><?php echo $cortesia ?></div>
                </td>
                <td align="center">
                    <div id="div_botao_<?php echo $idEmpresa ?>"><?php echo $confirmar ?></div>
                    <input type='button' name='adicionar' id='adicionar' value='Membros' onclick="window.location='addEmployee.php?id=<?php echo $idEmpresa ?>'"/>
                </td>
            </tr>
            <?php
                }
                $subTotalInscricao = $inscricao->valor - $inscricao->taxa;
            ?>
            <tr>
                <td align="center"><?php echo $idInscricao ?></td>
                <td align="center"><?php echo Funcoes::formata_data_para_exibir($inscricao->data_registro, true) ?></td>
                <td>
                    <?php echo $inscricao->id_individual . " - " . utf8_encode($inscricao->nome) ?><br>
                    <?php echo utf8_encode($inscricao->profissao) ?><br>
                    <?php echo $inscricao->email ?>
                </td>
                <td><?php echo $inscricao->descricao_tipo_inscricao ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($inscricao->valor) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($inscricao->taxa) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($subTotalInscricao) ?></td>
                <td align="right">
                    <?php
                    if (!empty($inscricao->data_pagamento)) {
                        echo Funcoes::formata_data_para_exibir($inscricao->data_pagamento);
                        echo "<br>";
                        echo (empty($inscricao->quem_registrou) ? "sistema" : $inscricao->quem_registrou);
                    }
                    ?>
                </td>
                <td colspan="2"></td>
            </tr>
            <?php
                $valorInscricaoEmpresa += $inscricao->valor;
                $valorTaxaEmpresa += $inscricao->taxa;
                $valorSubtotalEmpresa += $subTotalInscricao;
                $valorInscricao += $inscricao->valor;
                $contadorEmpresa++;

                if (empty($inscricao->data_pagamento)) {
                    $contadorEmAberto++;
                    $valorInscricaoEmAberto += $inscricao->valor;
                } else {
                    $contadorConfirmados++;
                    $valorInscricaoConfirmados += $inscricao->valor;
                    $valorTaxaInscricaoConfirmados += $inscricao->taxa;
                    $valorTotalInscricaoConfirmados += $subTotalInscricao;
                }
            }
            ?>
            <tr style="font-weight: bold">
                <td colspan="4">[ <?php echo $contadorEmpresa ?> ] inscrito(s)</td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorInscricaoEmpresa) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorTaxaEmpresa) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorSubtotalEmpresa) ?></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="10">&nbsp;</td>
            </tr>
            <tr style="font-weight: bold; color: red">
                <td colspan="4">Valor total de [ <?php echo $contadorEmAberto ?> ] inscrito(s) em aberto</td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorInscricaoEmAberto) ?></td>
            </tr>
            <tr style="font-weight: bold; color: blue">
                <td colspan="4">Valor total de [ <?php echo $contadorConfirmados ?> ] inscrito(s) confirmados</td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorInscricaoConfirmados) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorTaxaInscricaoConfirmados) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorTotalInscricaoConfirmados) ?></td>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr style="font-weight: bold; color: green">
                <td colspan="4">Valor total de [ <?php echo $contador ?> ] inscrito(s)</td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorInscricao) ?></td>
            </tr>
        </table>
    </body>
</html>