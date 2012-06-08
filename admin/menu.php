<?php
require 'validaSessao.php';
require_once '../general/autoload.php';

$o_inscricao = new InscricaoDAO();
$o_saque = new SaqueDAO();

$a_em_aberto = $o_inscricao->valor_total_inscritos("A");
$a_confirmados = $o_inscricao->valor_total_inscritos("C");
$a_confirmados_a_receber = $o_inscricao->valor_total_inscritos("CR");
$a_confirmados_disponivel = $o_inscricao->valor_total_inscritos("CD");
$a_cortesias = $o_inscricao->valor_total_inscritos("CO");
$a_cancelados = $o_inscricao->valor_total_inscritos("CA");
$a_incritos_instituicao = $o_inscricao->total_de_inscritos_por_instituicao();
$a_total_saques = $o_saque->total_saques();
$a_presentes = $o_inscricao->total_de_presentes();

$subtotal_confirmados = $a_confirmados_disponivel[0]->valor - $a_total_saques[0]->valor + $a_confirmados_a_receber[0]->valor;
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <title>Área Administrativa</title>
        <link href="css/admin.css" rel="stylesheet" />
    </head>
    <body>
        <center>
            <table>
                <?php
                foreach ($a_menu as $menu) {
                    $arquivo = $menu[0];
                    $opcao = $menu[1];
                    $perfis = $menu[2];
                    
                    if ($opcao && strstr($perfis, $perfil_usuario_logado)) {
                ?>
                <tr>
                    <td><a href="<?php echo "$arquivo.php" ?>"><?php echo $opcao ?></a></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <?php
                    }
                }
                ?>
            </table>
        </center>
        
        <?php
        if ($perfil_usuario_logado == "admin") {
        ?>
            <hr><br>
            <table border="1" class="bordasimples" width="30%">
                <tr>
                    <td colspan="3" style="font-weight: bold; text-align:center">Resumo das Inscrições</td>
                </tr>
                <tr style="font-weight: bold; text-align:center">
                    <td>Qtd.</td>
                    <td>Descrição</td>
                    <td>Valor (R$)</td>
                </tr>
                <tr style="color:red; font-weight:bold">
                    <td align="center"><?php echo $a_em_aberto[0]->quantidade ?></td>
                    <td>Em aberto</td>
                    <td align="right">+ <?php echo Funcoes::formata_moeda_para_exibir($a_em_aberto[0]->valor) ?></td>
                </tr>
                <tr style="color:maroon; font-weight:bold">
                    <td align="center"><?php echo $a_cancelados[0]->quantidade ?></td>
                    <td>Canceladas</td>
                    <td align="right"></td>
                </tr>
                <tr>
                    <td colspan="3" style="color:blue; font-weight: bold; text-align:center">Confirmadas</td>
                </tr>
                <tr>
                    <td align="center"><?php echo $a_confirmados_disponivel[0]->quantidade ?></td>
                    <td>Disponível</td>
                    <td align="right">+ <?php echo Funcoes::formata_moeda_para_exibir($a_confirmados_disponivel[0]->valor) ?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>Saques (<?php echo $a_total_saques[0]->total ?>)</td>
                    <td align="right">- <?php echo Funcoes::formata_moeda_para_exibir($a_total_saques[0]->valor) ?></td>
                </tr>
                <tr>
                    <td align="center"><?php echo $a_confirmados_a_receber[0]->quantidade ?></td>
                    <td>A receber</td>
                    <td align="right">+ <?php echo Funcoes::formata_moeda_para_exibir($a_confirmados_a_receber[0]->valor) ?></td>
                </tr>
                <tr style="color:maroon">
                    <td align="center"><?php echo $a_cortesias[0]->quantidade ?></td>
                    <td>Cortesias</td>
                    <td align="right"></td>
                </tr>
                <tr style="color:blue; font-weight:bold">
                    <td align="center"><?php echo $a_confirmados[0]->quantidade ?></td>
                    <td>Subtotal</td>
                    <td align="right">= <?php echo Funcoes::formata_moeda_para_exibir($subtotal_confirmados) ?></td>
                </tr>
                <tr style="color:navy; font-weight:bold">
                    <td align="center"><?php echo $a_presentes[0]->quantidade ?></td>
                    <td>Presentes</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3" style="color:green; font-weight: bold; text-align:center">Montante</td>
                </tr>
                <tr style="color:green; font-weight:bold">
                    <td align="center"><?php echo $a_em_aberto[0]->quantidade + $a_confirmados[0]->quantidade ?></td>
                    <td>Total</td>
                    <td align="right">= <?php echo Funcoes::formata_moeda_para_exibir($a_em_aberto[0]->valor + $subtotal_confirmados) ?></td>
                </tr>
            </table>
        <?php
        }
            if ($a_incritos_instituicao) {
        ?>
            <br><br>
            <table border="1" class="bordasimples">
                <tr>
                    <td colspan="2" style="font-weight: bold; text-align:center">Ranking por Instituição</td>
                </tr>
                <tr align="center" style="font-weight: bold">
                    <td>Nome</td>
                    <td>Inscritos</td>
                </tr>
        <?php
                $total_inscritos = 0;
                foreach($a_incritos_instituicao as $inscritos) {
                    $total_inscritos += $inscritos->quantidade;
        ?>
                <tr>
                    <td><?php echo utf8_encode($inscritos->instituicao) ?></td>
                    <td align="center"><?php echo $inscritos->quantidade ?></td>
                </tr>
        <?php
                }
        ?>
            <tr>
                <td><b>Total</b></td>
                <td align="center"><b><?php echo $total_inscritos ?></b></td>
            </tr>
            </table>
        <?php
            }
        ?>
    </body>
</html>