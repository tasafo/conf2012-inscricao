<?php
require 'validaSessao.php';
require_once '../general/autoload.php';

if (isset($_POST['data_saque']) && isset($_POST['valor'])) {
    $data = $_POST['data_saque'];
    $valor = $_POST['valor'];
    
    echo "<span style='color:red'>";
    
    if (!Funcoes::checa_data($data))
        echo "Informe uma data válida";
    else
        if ($valor <= 0)
            echo "Informe um valor válido";
        else {
            $o_saque = new SaqueDAO();
            $o_saque->data_saque = Funcoes::formata_data_para_gravar($data);
            $o_saque->valor = Funcoes::formata_moeda_para_gravar($valor);
            $o_saque->quem_registrou = $_SESSION['logado']['login'];
            $o_saque->salva();
            
            echo "<script>window.location='saques.php'</script>";
        }
    echo "</span>";
}

$o_saque = new SaqueDAO();
$a_saques = $o_saque->busca();
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
      <meta charset="utf-8">
          <title>Saques</title>
          <script type="text/javascript" src="../view/js/jquery/jquery.js" ></script>
          <script type="text/javascript" src="../view/js/jquery/jquery.validate.js" ></script>
          <script type="text/javascript" src="../view/js/validacao.js" ></script>
          <link href="css/admin.css" rel="stylesheet" />
    </head>
    <body onload="document.formSaque.data_saque.focus()">
        <h2><center>Saques</center></h2>
        <form id="formSaque" name="formSaque" action="saques.php" method="post">
            <table width="20%">
                <tr>
                    <td align="right">Data</td>
                    <td align="left"><input type="text" name="data_saque" id="data_saque" maxlength="10" size="10" onkeypress='mascara(this,data);' onblur='validaData(this);'/></td>
                </tr>
                <tr>
                    <td align="right">Valor</td>
                    <td align="left"><input type="text" name="valor" id="valor" maxlength="10" size="10" onKeyUp='this.value = soValorC(this.value, 2)' style='text-align: right'/></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" id="grava_saque" name="grava_saque" value="Registrar Saque" /></td>
                </tr>
            </table>
        </form>
        
        <?php if ($a_saques) { ?>
        <div id="div_grade_saques">
            <table width="40%" border="1" class="bordasimples">
                <tr style="font-weight: bold; text-align: center">
                    <td>Data</td>
                    <td>Valor</td>
                    <td>Quem registrou</td>
                </tr>
                <?php foreach ($a_saques as $saque) { ?>
                <tr id="row_<?php echo $saque->id ?>">
                    <td><?php echo Funcoes::formata_data_para_exibir($saque->data_saque) ?></td>
                    <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($saque->valor) ?></td>
                    <td><?php echo $saque->quem_registrou ?></td>
                    <?php $total += $saque->valor ?>
                </tr>
                <?php } ?>
                <tr style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($total) ?></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
        <?php } ?>
    </body>
</html>