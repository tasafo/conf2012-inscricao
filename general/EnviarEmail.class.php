<?php
require_once dirname(__FILE__) . '/autoload.php';
require_once dirname(__FILE__) . '/../util/constantes.php';

class EnviarEmail {
    public static function enviar($motivo, $tipo, $email, $nome, $id = 0, $complemento = "", $anexo = "") {
        if ($motivo == "cadastro")
            $titulo = "Cadastro realizado com sucesso";
        elseif ($motivo == "pagamento")
            $titulo = "Confirmação de pagamento e inscrição";
        elseif ($motivo == "aviso")
            $titulo = "Aviso";
    
        $mail = new PHPMailer();
        $mail->From = SENDMAIL_FROM;
        $mail->FromName = SENDMAIL_FROM_NAME;
        $mail->Host = SENDMAIL_HOST;
        $mail->IsMail();
        $mail->IsHTML(true);
        $mail->AddAddress($email, $nome);
        $mail->Subject = NOME_EVENTO . " - $titulo";

        $saudacao = $tipo == "individual" ? "Ol&aacute; " : "";
        
        $texto = "
            <html>
            <body>
            $saudacao<b>$nome</b>,<br><br>";
            
        $tipoCapitulado = ucfirst($tipo);
        
        if ($motivo == "cadastro") {
            $texto .= "
                Obrigado pelo interesse em participar do <b>" . NOME_EVENTO . "</b>!<br><br>
                <b>Confirma&ccedil;&atilde;o da Pr&eacute;-Inscri&ccedil;&atilde;o:</b><br>
                Confirmamos o cadastro de seus dados, voc&ecirc; est&aacute; inscrito com o c&oacute;digo de <b>n&uacute;mero $id</b>.<br><br>
                <b>Pagamento:</b><br>
                Estamos aguardando a confirma&ccedil;&atilde;o do PagSeguro, para finalizarmos seu processo de inscri&ccedil;&atilde;o.<br><br>
                Assim que conclu&iacute;do, voc&ecirc; receber&aacute; uma mensagem de confirma&ccedil;&atilde;o da inscri&ccedil;&atilde;o no <b>" . NOME_EVENTO . "</b>.<br><br>
                Caso tenha ocorrido algum problema, utilize o link abaixo para efetuar o pagamento e confirmar a inscri&ccedil;&atilde;o.<br>
                <a href='" . HOME_PAGE . "inscricao/view/pagamento$tipoCapitulado.php?id=" . $id . "'>" . HOME_PAGE . "inscricao/view/pagamento$tipoCapitulado.php?id=" . $id . "</a><br><br>
                $complemento";
                
        } elseif ($motivo == "pagamento") {
            if ($tipo == "individual") {
                $texto .= "
                    Escrevemos para informar que recebemos o pagamento de sua inscri&ccedil;&atilde;o.<br><br>";
            } elseif ($tipo == "empresa") {
                $texto .= "
                    Escrevemos para informar que recebemos o pagamento da inscri&ccedil;&atilde;o de seus funcion&aacute;rios.<br><br>
                    $complemento";
            }
        
        } elseif ($motivo == "aviso") {
            if (!empty($complemento)) {
                $texto .= "$complemento<br><br>";
            } else {    
                $texto .= "
                    Verificamos em nosso sistema que seu pagamento ainda n&atilde;o foi efetuado.<br><br>
                    Assim que conclu&iacute;do, voc&ecirc; receber&aacute; uma mensagem de confirma&ccedil;&atilde;o.<br><br>
                    Caso tenha ocorrido algum problema, utilize o link abaixo para efetuar o pagamento e confirmar sua inscri&ccedil;&atilde;o.<br><br>
                    <a href='" . HOME_PAGE . "inscricao/view/pagamento$tipoCapitulado.php?id=" . $id . "'>" . HOME_PAGE . "inscricao/view/pagamento$tipoCapitulado.php?id=" . $id . "</a><br><br>";
            }
        } elseif ($motivo == "envio_certificado") {
            if (file_exists($anexo))
                $mail->AddAttachment($anexo);
            
            $texto .= "
                Queremos agradecer sua participa&ccedil;&atilde;o no <b>" . NOME_EVENTO . "</b>.<br><br>
                Estamos enviando em anexo seu certificado e at&eacute; o pr&oacute;ximo evento.<br><br>";
        }

        $texto .= "<br>
            <b>Organiza&ccedil;&atilde;o do " . NOME_EVENTO . ".</b><br><br>
            Acesse nosso <a href='" . HOME_PAGE . "'>web site</a> ou siga o <a href='" . TWITTER_ENDERECO . "'>" . TWITTER_NOME . "</a> no Twitter para acompanhar as novidades do <b>" . NOME_EVENTO . "</b>.
            </body>
            </html>";
        
        $mail->Body = $texto;
   
        return $mail->Send();
    } 
}