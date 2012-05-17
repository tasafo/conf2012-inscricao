<?php
require_once '../general/autoload.php';
require_once '../util/constantes.php';

$msg = "";
$cor = "red";

if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    $o_individual = new IndividualDAO();
    
    $a_individual = $o_individual->busca("email = '$email' AND situacao = 'A' AND presente = 'S'");
    
    if ($a_individual) {
        $nome = utf8_encode($a_individual[0]->nome);
        
        require_once(dirname(__FILE__) . "/lib/fpdf/fpdf.php");
        require_once(dirname(__FILE__) . "/lib/fpdi/fpdi.php");
        
        $modelo = dirname(__FILE__) . "/template_certificado.pdf";
        
        $nome_arquivo = "Certificado " . NOME_EVENTO . " participante " . Funcoes::remove_acentos($nome) . ".pdf";
        $nome_arquivo = strtolower(str_replace(" ", "_", $nome_arquivo));
        $arquivo_destino = dirname(__FILE__) . "/tmp/$nome_arquivo";

        $nome_convertido = Funcoes::special_ucwords($nome);
        
        $titulo = "CERTIFICADO";

        $corpo = utf8_decode("Certificamos que $nome_convertido participou do evento " . NOME_EVENTO . ", realizado de 9 a 10 de Junho de 2012, no campus do CESUPA Almirante Barroso, Belém (Pa), com carga horária de 16 horas, na qualidade de participante.");

        $pdf = new FPDI();
        $pdf->AddPage('L');
        $pdf->setSourceFile($modelo);
        $tplIdx = $pdf->importPage(1);
        $pdf->useTemplate($tplIdx);                
        
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
        $pdf->MultiCell(0, 9, $corpo, 0, 1, 'J');

        $pdf->Output($arquivo_destino, 'F');
        
        $retorno = EnviarEmail::enviar("envio_certificado", "", $email, $nome, 0, "", $arquivo_destino);
          
        if (file_exists($arquivo_destino)) unlink($arquivo_destino);
        
        if ($retorno) {
            $msg = "O certificado foi enviado em anexo para seu e-mail. Obrigado e até o próximo evento.";
            $cor = "blue";
        } else
            $msg = "Não foi possível enviar o certificado para seu e-mail. Tente novamente.";
            
    } else {
        $msg = "E-mail não encontrando ou sua presença não foi registrada no evento.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <title><?php echo NOME_EVENTO ?> - Emissão de Certificado</title>
    </head>
    <body>
        <div id="main_header">
            <div id="header">
                <div id="texto">
                    <h4>Digite o e-mail que foi informado na sua inscrição para que o sistema possa emitir o certificado e enviá-lo.</h4>
                    <form id="form" name="form" action="index.php" method="post">
                        <input type="text" name="email" id="email" maxlength="100" size="30" />
                        <input type="submit" id="emitir" name="emitir" value="Emitir certificado" />
                    </form>
                    <br><br>
                    <p style="color: <?php echo $cor?>"><b><?php echo $msg ?></b></p>
                </div>
            </div>
        </div>
    </body>
</html>