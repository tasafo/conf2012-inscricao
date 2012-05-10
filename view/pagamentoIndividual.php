<?php
require_once '../general/autoload.php';
require_once '../util/constantes.php';
require_once '../util/pagseguro/pgs.php';

$mensagem_erro = "<center><h2>Informa&ccedil;&otilde;es incorretas</h2></center>";

if (intval($_REQUEST['id']) == 0)
    die($mensagem_erro);

$o_individual = new IndividualDAO();
$o_inscricao = new InscricaoDAO();
$o_tipo_inscricao = new TipoInscricaoDAO();

if (!$o_individual->busca($_REQUEST['id']))
    die($mensagem_erro);

if (!$o_inscricao->busca($o_individual->id_inscricao))
    die($mensagem_erro);

if (!$o_tipo_inscricao->busca($o_inscricao->id_tipo_inscricao))
    die($mensagem_erro);

// Criando um novo carrinho no pagseguro
// OBS.: na referencia da transacao sera colocado I(ndividual) e E(mpresa) antes do cpf
$pgs = new pgs(array(
    'email_cobranca' => EMAIL_COBRANCA,
    'tipo' => 'CP',
    'moeda' => 'BRL',
    'ref_transacao' => "I" . $o_individual->id
));

$pgs->cliente(array(
    'nome' => Funcoes::remove_acentos(utf8_encode($o_individual->nome)),
    'cep' => $o_individual->cep,
    'pais' => 'BRA',
    'email' => $o_individual->email
));

// Adicionando um produto
$pgs->adicionar(array(
    array(
        "descricao" => "Inscricao " . Funcoes::remove_acentos(NOME_EVENTO),
        "valor" => $o_tipo_inscricao->valor,
        "peso" => 0,
        "quantidade" => 1,
        "id" => $o_tipo_inscricao->id
    ),
));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<link href="css/estilo.css" rel="stylesheet" />
    <style type="text/css">
        body { color:#596b3a; }
    </style>
</head>
<body>
    <b>Inscrição Individual</b>
    <br>
    <br>
    <b>Olá <?php echo utf8_encode($o_individual->nome) ?></b>,
    <br>
    <br>Para finalizar o processo de inscrição, efetue o
    pagamento da mesma clicando no botão abaixo.
    <br>
    <br>Assim que recebermos a notificação de seu pagamento
    você será comunicado(a) por email.
    <br>
    <br>O pagamento deverá ser realizado via PagSeguro.
    <br>
    <br>
    <center>
    <br>
<?php
// Mostrando o botao de pagamento
$pgs->mostra();
?>
</center>
</body>
</html>
