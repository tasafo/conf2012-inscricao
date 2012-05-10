<?php
require 'validaSessao.php';
require_once '../general/autoload.php';

$o_inscricao = new InscricaoDAO();
$a_pagamentos = $o_inscricao->total_de_pagamentos_por_compensacao();

if (!$a_pagamentos) {
    echo '<center><h3><a href="menu.php">Voltar ao Menu</a></h3><br>';
    die("<h2>Nenhuma inscri&ccedil;&atilde;o encontrada</h2></center>");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
        <title>Pagamentos por Compensação</title>
        <link href="css/admin.css" rel="stylesheet" />
    </head>
    <body>
        <center>
            <h3><a href="menu.php">Voltar ao Menu</a></h3>
            <h2>Pagamentos por Compensação</h2>
        </center>
        <table width="20%" border="1" class="bordasimples">
            <tr style="font-weight: bold">
                <td align="center">Data</td>
                <td align="right">Valor</td>
            </tr>
            <?php
            $total = 0;
            foreach ($a_pagamentos as $pagamento) {
            ?>
            <tr>
                <td align="center"><?php echo Funcoes::formata_data_para_exibir($pagamento->data_compensacao) ?></td>
                <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($pagamento->valor) ?></td>
            <?php
                $total += $pagamento->valor;
            }
            ?>
            </tr>
            <tr>
                <td><b>Total</b></td>
                <td align="right"><b><?php echo Funcoes::formata_moeda_para_exibir($total) ?></b></td>
            </tr>
        </table>
    </body>
</html>