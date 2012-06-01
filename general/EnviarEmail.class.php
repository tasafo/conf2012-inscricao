<?php
require_once dirname(__FILE__) . '/autoload.php';
require_once dirname(__FILE__) . '/../util/constantes.php';

class EnviarEmail {
    public static function enviar($motivo, $tipo, $email, $nome, $id = 0, $complemento = "", $anexo = "") {
        $mail = new PHPMailer();
        $mail->From = SENDMAIL_FROM;
        $mail->FromName = SENDMAIL_FROM_NAME;
        $mail->Host = SENDMAIL_HOST;
        $mail->IsMail();
        $mail->IsHTML(true);
        $mail->AddAddress($email, $nome);

        $tipoCapitulado = ucfirst($tipo);

        $saudacao = $tipo == "individual" ? "Olá " : "";
        
        $primeiro_nome = array_shift(explode(" ", $nome));
        
        $texto = "<!DOCTYPE html><html lang='pt-br'><head><meta charset='utf-8'></head><body>$saudacao<b>$primeiro_nome</b>,<br><br>";

        switch ($motivo) {
            case 'cadastro':
                $titulo = "Cadastro realizado com sucesso";
                
                $texto .= "Obrigado pelo interesse em participar do <b>" . NOME_EVENTO . "</b>!<br><br>
                    <b>Confirma&ccedil;&atilde;o da Pr&eacute;-Inscri&ccedil;&atilde;o:</b><br>
                    Confirmamos o cadastro de seus dados, voc&ecirc; est&aacute; inscrito com o c&oacute;digo de <b>n&uacute;mero $id</b>.<br><br>
                    <b>Pagamento:</b><br>
                    Estamos aguardando a confirma&ccedil;&atilde;o do PagSeguro, para finalizarmos seu processo de inscri&ccedil;&atilde;o.<br><br>
                    Assim que conclu&iacute;do, voc&ecirc; receber&aacute; uma mensagem de confirma&ccedil;&atilde;o da inscri&ccedil;&atilde;o no <b>" . NOME_EVENTO . "</b>.<br><br>
                    Caso tenha ocorrido algum problema, utilize o link abaixo para efetuar o pagamento e confirmar a inscri&ccedil;&atilde;o.<br>
                    <a href='" . HOME_PAGE . "inscricao/view/pagamento$tipoCapitulado.php?id=" . $id . "'>" . HOME_PAGE . "inscricao/view/pagamento$tipoCapitulado.php?id=" . $id . "</a><br><br>
                    $complemento";
                break;
                
            case 'pagamento':
                $titulo = "Confirmação de pagamento e inscrição";
                
                if ($tipo == "individual")
                    $texto .= "Escrevemos para informar que recebemos o pagamento de sua inscri&ccedil;&atilde;o.<br><br>";
                elseif ($tipo == "empresa")
                    $texto .= "Escrevemos para informar que recebemos o pagamento da inscri&ccedil;&atilde;o de seus funcion&aacute;rios.<br><br>$complemento";
                break;
                
            case 'envio_certificado':
                $titulo = "Certificado de Participação";
                
                if (file_exists($anexo))
                    $mail->AddAttachment($anexo);
                
                $texto .= "Queremos agradecer sua participa&ccedil;&atilde;o e colabora&ccedil;&atilde;o no <b>" . NOME_EVENTO . "</b>.<br><br>
                    Tamb&eacute;m estamos enviando, em anexo, seu certificado de participa&ccedil;&atilde;o. Nos vemos nos pr&oacute;ximos eventos!<br><br>";                
                break;
                
            default:
                $titulo = "Aviso";
                
                if (!empty($complemento))
                    $texto .= "$complemento<br><br>";
                else {    
                    $texto .= "Verificamos em nosso sistema que seu pagamento ainda n&atilde;o foi efetuado.<br><br>
                        
                        Estamos chegando ao n&uacute;mero m&aacute;ximo da lota&ccedil;&atilde;o do evento. Precisamos que voc&ecirc; confirme sua participa&ccedil;&atilde;o para que a mesma n&atilde;o seja cancelada na pr&oacute;xima sexta-feira. Assim, podermos dar oportunidade a outras pessoas que desejam participar.<br><br>
                        
                        Assim que conclu&iacute;do, voc&ecirc; receber&aacute; uma mensagem de confirma&ccedil;&atilde;o.<br><br>
                        Caso tenha ocorrido algum problema, utilize o link abaixo para efetuar o pagamento e confirmar sua inscri&ccedil;&atilde;o.<br><br>
                        <a href='" . HOME_PAGE . "inscricao/view/pagamento$tipoCapitulado.php?id=" . $id . "'>" . HOME_PAGE . "inscricao/view/pagamento$tipoCapitulado.php?id=" . $id . "</a><br><br>";
                }
        }

        if ($motivo != "envio_certificado")
            $texto .= "<br>Acesse nosso <a href='" . HOME_PAGE . "'>web site</a> ou siga o <a href='" . TWITTER_ENDERECO . "'>" . TWITTER_NOME . "</a> no Twitter para acompanhar as novidades do " . NOME_EVENTO . ".<br><br>";
        
        $texto .= "<b>Organiza&ccedil;&atilde;o do " . NOME_EVENTO . "</b></body></html>";

        $mail->Subject = $titulo;
        $mail->Body = $texto;
   
        return $mail->Send();
    } 
}