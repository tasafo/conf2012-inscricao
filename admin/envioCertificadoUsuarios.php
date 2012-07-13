<?php
require 'validaSessao.php';
require_once '../general/autoload.php';
require_once '../util/constantes.php';

$a_todos_perfis = array('palestrante', 'organizador', 'organizador, admin', 'organizador, palestrante', 'organizador, palestrante, admin');

$a_perfis = array('palestrante', 'organizador');

$operacao = "Incluir";

$select_perfis = "";
foreach ($a_todos_perfis as $perfil)
  $select_perfis .= "<option value='$perfil'>$perfil</option>";
  
$o_usuario = new UsuarioDAO();

$caminho = dirname(__FILE__);

if (!isset($_POST['id'])) {
  if (isset($_POST['grava_usuario'])) { // Validacao para gravar usuario
    $usuario = array();
    $usuario['id'] = $_POST['id_usuario'];
    $usuario['nome'] = $_POST['nome_usuario'];
    $usuario['email'] = $_POST['email_usuario'];
    $usuario['perfis'] = $_POST['perfis_usuario'];
    $usuario['senha'] = $_POST['senha_usuario'];
    $usuario['palestra'] = $_POST['palestra_usuario'];
    
    if (empty($usuario['nome']))
      $erros .= "Informe o nome do usuário<br>";
    else
      if (empty($usuario['email']))
        $erros .= "Informe o e-mail do usuário<br>";
      else {
        $o_usuario = new UsuarioDAO();
        $o_usuario->id = $usuario['id'];
        $o_usuario->nome = $usuario['nome'];
        $o_usuario->email = $usuario['email'];
        $o_usuario->perfis = $usuario['perfis'];
        $o_usuario->senha = ($usuario['senha'] ? md5($usuario['senha']) : "");
        $o_usuario->tema_palestra = $usuario['palestra'];

        if (!$o_usuario->salva())
          $erros .= "Falha ao tentar gravar dados do usuario: " . $o_usuario->erro_sql . "<br>";
        else
          unset($usuario);
      }
  } elseif (isset($_REQUEST['op']) && $_REQUEST['op'] == 'alterar') {
    $o_usuario = new UsuarioDAO();
    $operacao = "Alterar";
    
    if ($o_usuario->busca($_REQUEST['id_usuario'])) {
      $usuario = array();
      $usuario['id'] = $o_usuario->id;
      $usuario['nome'] = utf8_encode($o_usuario->nome);
      $usuario['email'] = $o_usuario->email;
      $usuario['perfis'] = $o_usuario->perfis;
      $usuario['palestra'] = utf8_encode($o_usuario->tema_palestra);
      
      $select_perfis = "";
      foreach ($a_todos_perfis as $perfil) {
        $selecionado = ($perfil == $usuario['perfis']) ? "selected='selected'" : "";
        
        $select_perfis .= "<option value='$perfil' $selecionado>$perfil</option>";
      }
    }
  } elseif (isset($_REQUEST['op']) && $_REQUEST['op'] == 'excluir') {
    $o_usuario = new UsuarioDAO();
    if ($o_usuario->busca($_REQUEST['id_usuario']))
      $o_usuario->deleta();
  }
  
  $a_usuarios = $o_usuario->busca(null, "perfis, nome");
  
} else {
  $selecionados = "";
  foreach($_POST['id'] as $id)
    $selecionados .= "$id, ";
  
  $selecionados = substr($selecionados, 0, strlen($selecionados) - 2);
  
  $a_usuarios = $o_usuario->busca("id IN($selecionados)", "perfis, nome");
  
  if ($a_usuarios) {
    $modelo = "$caminho/../certificado/" . TEMPLATE_CERTIFICADO;

    foreach($a_usuarios as $usuario) {
      $nome = $usuario->nome;
      $email = $usuario->email;
      $perfis_usuario = $usuario->perfis;
      $tema_palestra = $usuario->tema_palestra;

      foreach($a_perfis as $perfil) {
        if (strstr($perfis_usuario, $perfil)) {
          require_once("$caminho/../certificado/lib/write_html.php");

          $nome_arquivo = "Certificado " . NOME_EVENTO . " $perfil " . Funcoes::remove_acentos(utf8_encode($nome)) . ".pdf";
          $nome_arquivo = strtolower(str_replace(" ", "_", $nome_arquivo));
          $arquivo_destino = "$caminho/../temp/$nome_arquivo";

          $pdf = new PDF_HTML();
          $pdf->AddPage('L');
          $pdf->setSourceFile($modelo);
          $tplIdx = $pdf->importPage(1);
          $pdf->useTemplate($tplIdx);
          
          $palestra = ($perfil == "palestrante") ? ', com o tema "' . utf8_encode($tema_palestra) . '"' : "";
          
          $nome_convertido = utf8_encode($nome);
          
          $titulo = "CERTIFICADO";
          
          $corpo = utf8_decode("Certificamos que <b>$nome_convertido</b> participou do evento " . NOME_EVENTO . ", realizado " . PERIODO_EVENTO . ", " . LOCAL_EVENTO . ", com carga horária de " . CARGA_HORARIA_EVENTO ." horas, na qualidade de <b>$perfil</b>$palestra.");
          
          // Titulo
          $pdf->SetFont('Arial', 'B', 32);
          $pdf->SetTextColor(35, 142, 35); //Verde Floresta
          $pdf->SetXY(110, 35);
          $pdf->Write(0, $titulo);
          
          // Corpo do texto
          $pdf->SetFont('Arial', '', 22);
          $pdf->SetTextColor(35, 142, 35); //Verde Floresta
          $pdf->SetY("65");
          $pdf->SetX("20");
          $pdf->WriteHTML($corpo, 9);
        
          $pdf->Output($arquivo_destino, 'F');
          
          $retorno = EnviarEmail::enviar("envio_certificado", "", $email, $nome, 0, "", $arquivo_destino);
          
          if (file_exists($arquivo_destino)) unlink($arquivo_destino);
      
          echo "<br><br>O certificado de $perfil de <b>" . utf8_encode($nome) . "</b>" . ($retorno ? "" : " nao") . " foi enviado com sucesso";
        }
      }
    }
  }
  exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
        <title>Organizadores e Palestrantes</title>
        <script type="text/javascript" src="../view/js/jquery/jquery.js" ></script>
        <link href="css/admin.css" rel="stylesheet" />
        <script>
        function operacoes(operacao, id_usuario) {
          if (operacao == "excluir") {
            decisao = confirm("Confirma a exclusão do usuário?");
            if (!decisao)
              return false;
          }
          window.location="envioCertificadoUsuarios.php?op=" + operacao + "&id_usuario=" + id_usuario;
        }
        </script>
    </head>
    <body onload="document.formCadastro.nome_usuario.focus();">
        <center>
            <h2>Organizadores e Palestrantes</h2>
        </center>
        <fieldset>
            <legend><b><span style='color:blue'><?php echo $operacao ?> usuário</span></b></legend>
            <center><span style='color:red'><?php echo $erros ?></span></center>
            <form id="formCadastro" name="formCadastro" action="envioCertificadoUsuarios.php" method="post">
                <input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $usuario['id'] ?>" />
                <table width="100%">
                    <tr>
                        <td align="right"><b>Nome *</b></td>
                        <td align="left"><input type="text" name="nome_usuario" id="nome_usuario" maxlength="50" size="40" value="<?php echo $usuario['nome'] ?>" /></td>
                    </tr>
                    <tr>
                        <td align="right"><b>E-mail *</b></td>
                        <td align="left"><input type="text" name="email_usuario" id="email_usuario" maxlength="50" size="40" value="<?php echo $usuario['email'] ?>" /></td>
                    </tr>
                    <td align="right"><b>Perfil</b></td>
                    <td align="left">
                        <select name="perfis_usuario" id="perfis_usuario">
                        <?php echo $select_perfis ?>
                        </select>
                    </td>
                    <tr>
                        <td align="right"><b>Senha</b></td>
                        <td align="left"><input type="password" name="senha_usuario" id="senha_usuario" maxlength="30" size="20" value="<?php echo $usuario['senha'] ?>"/> ( <i>Se for administrador</i> )</td>
                    </tr>
                    <tr>
                        <td align="right"><b>Tema da Palestra</b></td>
                        <td align="left"><input type="text" name="palestra_usuario" id="palestra_usuario" maxlength="100" size="80" value="<?php echo $usuario['palestra'] ?>" /> ( <i>Se for palestrante</i> )</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><input type="submit" id="grava_usuario" name="grava_usuario" value="Gravar usuário" /></td>
                    </tr>
                </table>
            </form>
        </fieldset>
        <br>
        
        <?php if ($a_usuarios) { ?>
        <form id="form" method="post" action="envioCertificadoUsuarios.php">
            <table width="100%" border="1" class="bordasimples">
                <tr style="font-weight: bold" align="center">
                    <td align="center">&nbsp;</td>
                    <td>Id</td>
                    <td>Nome</td>
                    <td>E-mail</td>
                    <td>Perfis</td>
                    <td>Tem acesso</td>
                    <td>Tema da Palestra</td>
                    <td>Operações</td>
                </tr>
                <?php
                foreach ($a_usuarios as $usuario) {
                ?>
                <tr>
                    <td align="center"><input type='checkbox' name='id[]' value='<?php echo $usuario->id ?>' /></td>
                    <td><?php echo $usuario->id ?></td>
                    <td><?php echo utf8_encode($usuario->nome) ?></td>
                    <td><?php echo $usuario->email ?></td>
                    <td><?php echo $usuario->perfis ?></td>
                    <td style="color: <?php echo ($usuario->senha ? 'blue' : 'red') ?>" ><?php echo ($usuario->senha ? "Sim" : "Não") ?></td>
                    <td><?php echo utf8_encode($usuario->tema_palestra) ?></td>
                    <td align="center">
                      <a href="#" onclick="operacoes('alterar', <?php echo $usuario->id ?>)" title="Alterar">Alt.</a> |
                      <a href="#" onclick="operacoes('excluir', <?php echo $usuario->id ?>)" title="Excluir">Exc.</a>
                    </td>
                <?php
                }
                ?>
                <tr>
                    <td colspan="8" align="center"><b><?php echo count($a_usuarios) ?> pessoas envolvidas</b></td>
                </tr>
            </table>
            <center>
              <br>
              <b style="color:blue">Selecione o(s) usuário(s) e clique no botão abaixo para enviar os certificados aos destinatários por e-mail.</b><br><br>
              <input type='submit' name='enviar' id='enviar' value='Enviar Certificados' /><br><br>
            </center>
        </form>
        <?php } ?>
    </body>
</html>