<?php
require 'validaSessao.php';
require_once '../general/autoload.php';
require_once '../util/constantes.php';

$a_perfis = array('palestrante', 'organizador');

$o_usuario = new UsuarioDAO();
$a_usuarios = $o_usuario->busca(null, "perfis, nome");

if (isset($_POST['enviar'])) {
  if ($a_usuarios) {
    $modelo = dirname(__FILE__) . "/../certificado/template_certificado.pdf";

    foreach($a_usuarios as $usuario) {
      $nome = $usuario->nome;
      $email = $usuario->email;
      $perfis_usuario = $usuario->perfis;
      $tema_palestra = $usuario->tema_palestra;

      foreach($a_perfis as $perfil) {
        if (strstr($perfis_usuario, $perfil)) {
          require_once(dirname(__FILE__) . "/../certificado/lib/fpdf/fpdf.php");
          require_once(dirname(__FILE__) . "/../certificado/lib/fpdi/fpdi.php");

          $nome_arquivo = "Certificado " . NOME_EVENTO . " $perfil " . Funcoes::remove_acentos(utf8_encode($nome)) . ".pdf";
          $nome_arquivo = strtolower(str_replace(" ", "_", $nome_arquivo));
          $arquivo_destino = dirname(__FILE__) . "/tmp/$nome_arquivo";

          $pdf = new FPDI();
          $pdf->AddPage('L');
          $pdf->setSourceFile($modelo);
          $tplIdx = $pdf->importPage(1);
          $pdf->useTemplate($tplIdx);
          
          $pdf->SetFont('Arial', '', 22);
          $pdf->SetTextColor(255, 255, 255);
          
          $palestra = ($perfil == "palestrante") ? ', com o tema "' . utf8_encode($tema_palestra) . '"' : "";
          
          $nome_convertido = utf8_encode($nome);
          
          $texto = utf8_decode("Certificamos que $nome_convertido participou do evento " . NOME_EVENTO . ", realizado de 9 a 10 de Junho de 2012, no campus do CESUPA Almirante Barroso, Belém (Pa), com carga horária de 16 horas, na qualidade de $perfil$palestra.");
          
          $pdf->SetY("100");
          $pdf->SetX("20");
          $pdf->MultiCell(0, 9, $texto, 0, 1, 'J');

          $pdf->Output($arquivo_destino, 'F');
          
          $retorno = EnviarEmail::enviar("envio_certificado", "", $email, $nome, 0, "", $arquivo_destino);
          
          if (file_exists($arquivo_destino)) unlink($arquivo_destino);
      
          echo "O certificado de $perfil de <b>" . utf8_encode($nome) . "</b>" . ($retorno ? "" : " nao") . " foi enviado com sucesso<br><br>";
        }
      }
    }
  }
  die("<center><h3><a href='menu.php'>Voltar ao Menu</a></h3></center>");
} else {
  if (!$a_usuarios) {
      die("<center><h3><a href='menu.php'>Voltar ao Menu</a></h3><br><h2>Nenhum usu&aacute;rio encontrado</h2></center>");
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
        <title>Envio de Certificado aos Usuários</title>
        <script type="text/javascript" src="../view/js/jquery/jquery.js" ></script>
        <script type="text/javascript" src="../view/js/jquery/jquery.alerts/jquery.alerts.js" ></script>
        <link href="css/admin.css" rel="stylesheet" />
    </head>
    <body>
        <center>
            <h3><a href="menu.php">Voltar ao Menu</a></h3>
            <h2>Envio de Certificado aos Usuários</h2>
        </center>
        <form id="form" method="post" action="envioCertificadoUsuarios.php">
            <table width="100%" border="1" class="bordasimples">
                <tr style="font-weight: bold">
                    <td>Id</td>
                    <td>Nome</td>
                    <td>E-mail</td>
                    <td>Perfis</td>
                </tr>
                <?php
                foreach ($a_usuarios as $usuario) {
                ?>
                <tr>
                    <td><?php echo $usuario->id ?></td>
                    <td><?php echo utf8_encode($usuario->nome) ?></td>
                    <td><?php echo $usuario->email ?></td>
                    <td><?php echo $usuario->perfis ?></td>
                <?php
                }
                ?>
            </table>
            <center>
              <br><br>
              <b style="color:red">Quando tiver certeza do envio, clique no botão abaixo para iniciar o processo</b><br><br>
              <input type='submit' name='enviar' id='enviar' value='Enviar Certificados' /><br><br>
            </center>
        </form>
    </body>
</html>