<?php
require_once '../general/autoload.php';
require_once '../util/constantes.php';

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    
    $o_individual = new IndividualDAO();
    
    $a_individual = $o_individual->busca("email = '$email' AND situacao = 'A' AND presente = 'S'");
    if ($a_individual) {
        $nome = $a_individual[0]->nome;
        
        require_once(dirname(__FILE__) . "/lib/fpdf/fpdf.php");
        require_once(dirname(__FILE__) . "/lib/fpdi/fpdi.php");
        $modelo = "template_certificado.pdf";
        $arquivo_destino = "Certificado " . NOME_EVENTO . ".pdf";

        $pdf = new FPDI();
        $pdf->AddPage('L');
        $pdf->setSourceFile($modelo);
        $tplIdx = $pdf->importPage(1);
        $pdf->useTemplate($tplIdx);
        
        $pdf->SetFont('Arial', '', 22);
        $pdf->SetTextColor(255, 255, 255);
        
        $nome_convertido = utf8_encode($nome);
          
        $texto = utf8_decode("Certificamos que $nome_convertido participou do evento " . NOME_EVENTO . ", realizado de 9 a 10 de Junho de 2012, no campus do CESUPA Almirante Barroso, Belém (Pa), com carga horária de 16 horas, na qualidade de participante.");
        
        $pdf->SetY("100");
        $pdf->SetX("20");
        $pdf->MultiCell(0, 9, $texto, 0, 1, 'J');
        
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$arquivo_destino");
        header("Content-Type: application/pdf");
        header("Content-Transfer-Encoding: binary");

        $pdf->Output($arquivo_destino, 'D');
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
                    <h4>Informe seu e-mail de inscrição para emitir o certificado</h4>
                    <form id="form" name="form" action="index.php" method="post">
                        <input type="text" name="email" id="email" maxlength="100" size="30" />
                        <input type="submit" id="emitir" name="emitir" value="Emitir certificado" />
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>