<?php
require_once '../general/autoload.php';
require_once '../util/constantes.php';
require_once '../util/pagseguro/pgs.php';

$mensagem_erro = "<center><h2>Informa&ccedil;&otilde;es incorretas</h2></center>";

if (intval($_REQUEST['id']) == 0)
    die($mensagem_erro);

$o_empresa = new EmpresaDAO();
$o_individual = new IndividualDAO();
$o_inscricao = new InscricaoDAO();
$o_tipo_inscricao = new TipoInscricaoDAO();

if (!$o_empresa->busca($_REQUEST['id']))
    die($mensagem_erro);

$a_funcionarios_inscritos = $o_inscricao->selecionar_funcionarios_inscritos($o_empresa->id);

if (!$a_funcionarios_inscritos)
    die($mensagem_erro);

// Criando um novo carrinho no pagseguro
// OBS.: na referencia da transacao sera colocado I(ndividual) e E(mpresa) antes do cpf
$pgs = new pgs(array(
  'email_cobranca' => EMAIL_COBRANCA,
  'tipo' => 'CP',
  'moeda' => 'BRL',
  'ref_transacao' => "E" . $o_empresa->id
));

$pgs->cliente(array(
  'nome' => Funcoes::remove_acentos(utf8_encode($o_empresa->nome)),
  'cep' => $o_empresa->cep,
  'pais' => 'BRA',
  'email' => $o_empresa->email
));

// Adicionando os funcionarios no carrinho do PagSeguro
$a_carrinho = array();

if (count($a_funcionarios_inscritos) > 25) {
	$total_inscritos = 0;
	$valor_total_pagamento_unico = 0;
	
	foreach ($a_funcionarios_inscritos as $inscrito) {
		$valor_total_pagamento_unico += $inscrito->valor;
		$total_inscritos++;
	}

	$a_carrinho[] = array(
        "descricao" => "Inscricao " . Funcoes::remove_acentos(NOME_EVENTO) . " - Pagamento Unico ($total_inscritos inscricoes tipo Instituicao)",
        "valor" => $valor_total_pagamento_unico,
        "peso" => 0,
        "quantidade" => 1,
        "id" => $o_empresa->id
	);
} else {
	foreach ($a_funcionarios_inscritos as $inscrito) {
		$a_carrinho[] = array(
            "descricao" => "Inscricao " . Funcoes::remove_acentos(NOME_EVENTO) . " - " . trim(Funcoes::remove_acentos(utf8_encode($inscrito->nome))),
            "valor" => $inscrito->valor,
            "peso" => 0,
            "quantidade" => 1,
            "id" => $inscrito->id
		);
	}
}

$pgs->adicionar($a_carrinho);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<link href="css/estilo.css" rel="stylesheet" />
    <style type="text/css">
        body { color:#89383f; }
    </style>
</head>
<body>
<b>Inscrição por Empresa</b>
<br><br>
<b><?php echo utf8_encode($o_empresa->nome) ?></b>,<br><br>
Para finalizar o processo de inscrição, efetue o pagamento
da mesma clicando no botão abaixo.<br><br>
Assim que recebermos a notificação de seu pagamento você
será comunicado(a) por email.<br><br>
O pagamento deverá ser realizado via PagSeguro.<br><br>
<center><br>
<?php
// Mostrando o botao de pagamento
$pgs->mostra();
?>
</center>
</body>
</html>
