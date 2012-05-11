<?php
// Informacoes de acesso ao banco de dados
define('BANCO_USUARIO', 'usuario');
define('BANCO_SENHA', 'senha');
define('BANCO_BASE_DADOS', 'nome_do_banco');
define('BANCO_SERVIDOR', 'localhost');

// Informacoes utilizadas para o envio do email
define('SENDMAIL_FROM', 'contato@dominio.com.br');
define('SENDMAIL_FROM_NAME', 'Titulo do Evento');
define('SENDMAIL_HOST', 'mail.dominio.com.br');

// Informacoes utilizadas no e-mail de envio para o usuario 
define('NOME_EVENTO', 'Nome do Evento');
define('TWITTER_NOME', '@twitter');
define('TWITTER_ENDERECO', 'http://twitter.com/nomedoevento');
define('HOME_PAGE', 'http://www.site.com.br/');

// E-MAIL E TOKEN DA CONTA DO PAGSEGURO PARA OS PAGAMENTOS SEREM EFETUADOS NELA
define('EMAIL_COBRANCA', 'nome@mail.com.br');
define('TOKEN', '');

// Desativa o link de inscricoes quando o numero de inscritos chegar a esse tamanho ou a data expirar
define('QTD_MAXIMA_INSCRITOS', '200');
define('DATA_FINAL_INSCRICAO', 'DD/MM/AAAA');

// Exibe ou nao instrucoes sql na tela
define('EXIBIR_SQL', false);
?>
