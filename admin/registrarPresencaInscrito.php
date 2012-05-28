<?php
require 'validaSessao.php';
require_once '../general/autoload.php';

if (isset($_POST['nome'])) {
    $nome = trim($_POST['nome']);
    
    $o_inscricao = new InscricaoDAO();

    if ($nome != "")
        $a_inscritos = $o_inscricao->lista_para_confirmar_presenca("nome LIKE '%$nome%'", "nome");
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
            <b>Informe o nome do inscrito:</b> <input type="text" id="nome" name="nome" size="50" maxlength="50" />
            <input type='submit' name='buscar' id='buscar' value='Buscar' /><br><br>
            <center><span id="processando" style="color: red"></span></center>
            
            <?php if ($a_inscritos) { ?>
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
                    <td align="center">Pagamento</td>
                </tr>
                <?php
                foreach ($a_inscritos as $inscrito) {
                    $situacao = "<span style='color:red'>Cancelado</span>";
                    if ($inscrito->situacao == "A")
                        $situacao = "<span style='color:blue'>Ativo</span>";
                    
                    $presente = "<span style='color:red'>Não</span>";
                    if ($inscrito->presente == "S")
                        $presente = "<span style='color:blue'>Sim</span>";

                    $pagamento = "<span style='color:red'>Não realizado</span>";
                    if (!empty($inscrito->data_pagamento))
                        $pagamento = "<span style='color:blue'>Confirmado</span>";    
                ?>
                <tr>
                    <td align="center">
                        <?php if ($inscrito->presente != "S" && $inscrito->situacao == "A" && !empty($inscrito->data_pagamento)) { ?>
                        <input type='checkbox' name='id[]' value='<?php echo $inscrito->id ?>' />
                        <?php } else { ?>
                        &nbsp;
                        <?php } ?>
                    </td>
                    <td><?php echo utf8_encode($inscrito->nome) ?></td>
                    <td><?php echo $inscrito->email ?></td>
                    <td><?php echo utf8_encode($inscrito->instituicao) ?></td>
                    <td align="center"><?php echo $presente ?></td>
                    <td><?php echo $inscrito->quem_registrou_presenca ?></td>
                    <td align="center"><?php echo $situacao ?></td>
                    <td align="center"><?php echo $pagamento ?></td>
                <?php
                }
                ?>
            </table>
            <?php } ?>
        </form>
    </body>
</html>