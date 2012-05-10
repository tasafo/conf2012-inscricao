<?php
require 'validaSessao.php';
require_once '../general/autoload.php';

$ordem = "ind.instituicao, ind.nome";
if ($_GET['ordem'])
    $ordem = $_GET['ordem'];

$o_inscricao = new InscricaoDAO();

$a_inscritos = $o_inscricao->selecionar_inscritos_individual(true, $ordem, "T");

if (!$a_inscritos) {
    echo '<center><h3><a href="menu.php">Voltar ao Menu</a></h3><br>';
    die("<h2>Nenhuma inscri&ccedil;&atilde;o encontrada</h2></center>");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
        <title>Manutenção de Inscritos</title>
        <script type="text/javascript" src="../view/js/jquery/jquery.js" ></script>
        <script type="text/javascript" src="../view/js/jquery/jquery.alerts/jquery.alerts.js" ></script>
        <script type="text/javascript" src="js/manutencaoInscritos.js" ></script>
        <link href="../view/js/jquery/jquery.alerts/jquery.alerts.css" rel="stylesheet" />
        <link href="css/admin.css" rel="stylesheet" />
    </head>
    <body>
        <center>
            <h3><a href="menu.php">Voltar ao Menu</a></h3>
            <h2>Manutenção de Inscritos</h2>
        </center>
        <form id="form" action="post" action="">
            <b>Novo nome para a instituição:</b> <input type="text" id="novo_nome" name="novo_nome" size="30" maxlength="50" />
            <input type='button' name='mudar' id='mudar' value='Mudar' /><br><br>
            <input type='button' name='reativar' id='reativar' value='Reativar cancelados' /><br><br>
            <input type='button' name='presenca' id='presenca' value='Marcar presença' /><br><br>
            <center><span id="processando" style="color: red"></span></center>
            <table width="100%" border="1" class="bordasimples">
                <tr style="font-weight: bold">
                    <td>&nbsp;</td>
                    <td align="center">Id</td>
                    <td>Instituição</td>
                    <td>Nome</td>
                    <td>E-mail</td>
                    <td align="center">Situação</td>
                    <td align="center">Presente</td>
                </tr>
                <?php
                foreach ($a_inscritos as $inscritos) {
                ?>
                <tr>
                    <td align="center"><input type='checkbox' name='id[]' value='<?php echo $inscritos->id_individual ?>' /></td>
                    <td align="center"><?php echo $inscritos->id_individual ?></td>
                    <td><?php echo utf8_encode($inscritos->instituicao) ?></td>
                    <td><?php echo utf8_encode($inscritos->nome) ?></td>
                    <td><?php echo $inscritos->email ?></td>
                    <td align="center"><?php echo $inscritos->situacao ?></td>
                    <td align="center"><?php echo $inscritos->presente ?></td>
                <?php
                }
                ?>
            </table>
        </form>
    </body>
</html>