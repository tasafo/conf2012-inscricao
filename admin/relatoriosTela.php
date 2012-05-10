<?php
require 'validaSessao.php';
require_once '../general/autoload.php';
require_once 'relatorio.class.php';

$o_tipo_inscricao = new TipoInscricaoDAO();
$a_tipo_inscricao = $o_tipo_inscricao->busca();

$select = "";
foreach ($a_tipo_inscricao as $tipo_inscricao) {
    $select .= "<option value='" . $tipo_inscricao->id . "'>" . $tipo_inscricao->descricao . "</option>";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		<script type="text/javascript" src="../view/js/validacao.js" ></script>
		<script type="text/javascript" src="../view/js/ajax.js" ></script>
		<link type="text/css" href="../view/css/estilo.css" rel="stylesheet" />
	</head>
    <body>
        <h3><center><a href="menu.php">Voltar ao Menu</a></center></h3>
        <h1>Relat&oacute;rios</h1>
        <form name="frm_consulta" action="">
            <table align="center">
                <tr>
                    <td><label><input type="radio" name="opcao" value="periodo" checked="checked"/>Inscrições por Período</label></td>
                </tr>
                <tr>
                    <td><label><input type="radio" name="opcao" value="tipo_inscricao" />Por Tipo de Inscrição</label></td>
                </tr>
                <tr>
                    <td><label><input type="radio" name="opcao" value="inscricoes_confirmadas"/>Inscrições Confirmadas</label></td>
                </tr>
                <tr>
                    <td><label><input type="radio" name="opcao" value="inscricoes_confirmadas_periodo"/>Inscrições Confirmadas por Período do Pagamento</label></td>
                </tr>
                <tr>
                    <td><label><input type="radio" name="opcao" value="ordem_alfabetica" />Inscrições em Ordem Alfabética</label></td>
                </tr>
                <tr>
                    <td><label><input type="radio" name="opcao" value="inscricoes_canceladas"/>Inscrições Canceladas</label></td>
                </tr>
                <tr>
                    <td align="center"><input type="button" value="Confirmar" onclick="opcaoConsulta();"></td>
                </tr>
            </table>
            <div id="div_periodo" style="margin-top:20px;">
                <table align="center">
                    <tr>
                        <td>Data Inicial:</td>
                        <td>
                            <input type="text" name="data_inicio" id="data_inicio" maxlength="10" onkeypress="mascara(this,data);" onblur="validaData(this);" />
                        </td>
                    </tr>
                    <tr>
                        <td>Data Final:</td>
                        <td>
                            <input type="text" name="data_fim" id="data_fim" maxlength="10" onkeypress="mascara(this,data);" onblur="validaData(this);" />
                        </td>
                    </tr>
                </table>
            </div>
            <div id="div_tipo_inscricao"><br><br>
                <table class="bordasimples" align="center">
                    <tr align="center">
                        <td align="left" width="40%">Escolha um Tipo de Inscrição:</td>
                        <td align="left" width="60%">
                            <select name="tipo_inscricao" id="tipo_inscricao" style="width: 340px">
                                <?php echo $select ?>
                            </select>
                        </td>
                    </tr>
                </table><br>
            </div>
            <div id="div_botao">
                <table align="center">
                    <tr align="center">
                        <td align="center"><input type="button" value="Consultar" onclick="javascript:consultar();" /></td>
                    </tr>
                </table>
            </div>
            <div id="resultado"></div>
        </form>
    </body>
</html>