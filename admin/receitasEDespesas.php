<?php
require 'validaSessao.php';
require_once '../general/autoload.php';

if (isset($_POST['data_operacao']) &&
    isset($_POST['valor']) &&
    isset($_POST['responsavel']) &&
    isset($_POST['descricao'])) {
    
    $data = $_POST['data_operacao'];
    $tipo = $_POST['tipo'];
    $valor = $_POST['valor'];
    $responsavel = $_POST['responsavel'];
    $descricao = $_POST['descricao'];
    
    echo "<span style='color:red'>";
    
    if (!Funcoes::checa_data($data))
        echo "Informe uma data válida";
    else
        if ($valor <= 0)
            echo "Informe um valor válido";
        else {
            $o_receita_despesa = new ReceitaDespesaDAO();
            $o_receita_despesa->data_operacao = Funcoes::formata_data_para_gravar($data);
            $o_receita_despesa->tipo = $tipo;
            $o_receita_despesa->valor = Funcoes::formata_moeda_para_gravar($valor);
            $o_receita_despesa->responsavel = $responsavel;
            $o_receita_despesa->descricao = $descricao;
            $o_receita_despesa->quem_registrou = $_SESSION['logado']['login'];
            $o_receita_despesa->salva();
            
            echo "<script>window.location='receitasEDespesas.php'</script>";
        }
    echo "</span>";
}

$o_receita_despesa = new ReceitaDespesaDAO();
$a_receitas_despesas = $o_receita_despesa->busca();
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
      <meta charset="utf-8">
          <title>Receitas e Despesas</title>
          <script type="text/javascript" src="../view/js/jquery/jquery.js" ></script>
          <script type="text/javascript" src="../view/js/jquery/jquery.validate.js" ></script>
          <script type="text/javascript" src="../view/js/validacao.js" ></script>
          <link href="css/admin.css" rel="stylesheet" />
    </head>
    <body onload="document.formLancamento.data_operacao.focus()">
        <h2><center>Receitas e Despesas</center></h2>
        <form id="formLancamento" name="formLancamento" action="receitasEDespesas.php" method="post">
            <table width="20%">
                <tr>
                    <td align="right">Data</td>
                    <td align="left"><input type="text" name="data_operacao" id="data_operacao" maxlength="10" size="10" onkeypress='mascara(this, data);' onblur='validaData(this);'/></td>
                </tr>
                <tr>
                    <td align="right">Tipo</td>
                    <td align="left">
                      <select name="tipo" id="tipo">
                        <option value="C">Crédito</option>
                        <option value="D">Débito</option>
                      </select>
                    </td>
                </tr>
                <tr>
                    <td align="right">Valor</td>
                    <td align="left"><input type="text" name="valor" id="valor" maxlength="10" size="10" onKeyUp='this.value = soValorC(this.value, 2)' style='text-align: right'/></td>
                </tr>
                <tr>
                    <td align="right">Responsável</td>
                    <td align="left"><input type="text" name="responsavel" id="responsavel" maxlength="50" size="40" /></td>
                </tr>
                <tr>
                    <td align="right">Descrição</td>
                    <td align="left"><input type="text" name="descricao" id="descricao" maxlength="50" size="40" /></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" id="grava" name="grava" value="Registrar Operação" /></td>
                </tr>
            </table>
        </form>
        
        <?php if ($a_receitas_despesas) { ?>
        <div id="div_grade_extrato">
            <table width="80%" border="1" class="bordasimples">
                <tr style="font-weight: bold; text-align: center">
                    <td>Data</td>
                    <td>Valor</td>
                    <td>Responsável</td>
                    <td>Descrição</td>
                    <td>Quem registrou</td>
                </tr>
                <?php foreach ($a_receitas_despesas as $extrato) { ?>
                <tr id="row_<?php echo $extrato->id ?>">
                    <td align="center"><?php echo Funcoes::formata_data_para_exibir($extrato->data_operacao) ?></td>
                    <td align="right" style="color:<?php echo ($extrato->tipo == "C" ? "blue" : "red") ?>"><?php echo Funcoes::formata_moeda_para_exibir($extrato->valor) . " " . $extrato->tipo ?></td>
                    <td><?php echo utf8_encode($extrato->responsavel) ?></td>
                    <td><?php echo utf8_encode($extrato->descricao) ?></td>
                    <td><?php echo $extrato->quem_registrou ?></td>
                    <?php $saldo += ($extrato->tipo == "C" ? $extrato->valor : $extrato->valor * -1) ?>
                </tr>
                <?php } ?>
                <tr style="font-weight:bold">
                    <td>Saldo</td>
                    <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($saldo) ?>&nbsp;&nbsp;&nbsp;</td>
                    <td colspan="3">&nbsp;</td>
                </tr>
            </table>
        </div>
        <?php } ?>
    </body>
</html>