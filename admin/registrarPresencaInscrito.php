<?php
require 'validaSessao.php';
require_once '../general/autoload.php';

if (isset($_POST['nome'])) {
    $nome = trim($_POST['nome']);
    
    $o_individual = new IndividualDAO();

    if ($nome != "")
        $a_individuos = $o_individual->busca("nome LIKE '$nome%'", "nome");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
        <title>Registrar presença do inscrito</title>
        <script type="text/javascript" src="../view/js/jquery/jquery.js" ></script>
        <script type="text/javascript" src="../view/js/jquery/jquery.alerts/jquery.alerts.js" ></script>
        <script type="text/javascript" src="js/registrarPresencaInscrito.js" ></script>
        <script type="text/javascript" src="js/marcarPresenca.js" ></script>
        <link href="../view/js/jquery/jquery.alerts/jquery.alerts.css" rel="stylesheet" />
        <link href="css/admin.css" rel="stylesheet" />
    </head>
    <body onload="document.form.nome.focus();">
        <center>
            <h2>Registrar presença do inscrito</h2>
        </center>
        <form id="form" name="form" method="post" action="registrarPresencaInscrito.php">
            <b>Informe o início ou nome completo:</b> <input type="text" id="nome" name="nome" size="50" maxlength="50" />
            <input type='submit' name='buscar' id='buscar' value='Buscar' /><br><br>
            <center><span id="processando" style="color: red"></span></center>
            
            <?php if ($a_individuos) { ?>
            <center><br><input type='button' name='presenca' id='presenca' value='Marcar presença' /></center><br>
            <table width="100%" border="1" class="bordasimples">
                <tr style="font-weight: bold">
                    <td>&nbsp;</td>
                    <td>Nome</td>
                    <td>E-mail</td>
                    <td>Instituição</td>
                    <td align="center">Presente</td>
                    <td>Quem registrou</td>
                    <td align="center">Situação</td>
                </tr>
                <?php
                foreach ($a_individuos as $individuo) {
                    $situacao = "<span style='color:red'>Cancelado</span>";
                    if ($individuo->situacao == "A")
                        $situacao = "<span style='color:blue'>Ativo</span>";
                    
                    $presente = "<span style='color:red'>Não</span>";
                    if ($individuo->presente == "S")
                        $presente = "<span style='color:blue'>Sim</span>";
                ?>
                <tr>
                    <td align="center">
                        <?php if ($individuo->presente != "S" && $individuo->situacao == "A") { ?>
                        <input type='checkbox' name='id[]' value='<?php echo $individuo->id ?>' />
                        <?php } else { ?>
                        &nbsp;
                        <?php } ?>
                    </td>
                    <td><?php echo utf8_encode($individuo->nome) ?></td>
                    <td><?php echo $individuo->email ?></td>
                    <td><?php echo utf8_encode($individuo->instituicao) ?></td>
                    <td align="center"><?php echo $presente ?></td>
                    <td><?php echo $individuo->quem_registrou_presenca ?></td>
                    <td align="center"><?php echo $situacao ?></td>
                <?php
                }
                ?>
            </table>
            <?php } ?>
        </form>
    </body>
</html>