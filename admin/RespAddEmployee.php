<?php
session_start();
require_once '../general/autoload.php';

$idEmpresa = $_REQUEST['hdnIdEmpresa'];
$categoria_inscricao = $_REQUEST['func_categoria_inscricao'];
$nome = $_REQUEST['func_nome'];
$email = strtolower($_REQUEST['func_email']);
$profissao = $_REQUEST['func_profissao'];

$nome_sem_acento = Funcoes::remove_acentos($nome);

$a_campos = array("email" => $_REQUEST['func_email']);
foreach($a_campos as $campo => $valor) {
	$o_individual = new IndividualDAO();

	if ($o_individual->busca("$campo = '$valor' AND situacao = 'A'"))
	    die("Atencao! Este $campo ja foi utilizando em uma inscricao no sistema.");
}

$o_transacao = new Banco();
$o_transacao->begin();

$o_empresa = new EmpresaDAO();

if (!$o_empresa->busca($idEmpresa)) {
	die("Atencao! Empresa nao encontrada no sistema.");
}

// Obtem informacoes de pagamento dos funcionarios da empresa
$data_pagamento = "";
$data_compensacao = "";
$taxa_pagamento = 0;

$o_inscricao = new InscricaoDAO();
$a_busca_inscricao = $o_inscricao->busca("id_empresa = $idEmpresa");

if ($a_busca_inscricao) {
    $data_pagamento = $a_busca_inscricao[0]->data_pagamento;
    $data_compensacao = $a_busca_inscricao[0]->data_compensacao;
    $taxa_pagamento = $a_busca_inscricao[0]->taxa;
}

$o_inscricao = new InscricaoDAO();
$o_inscricao->id_empresa = $idEmpresa;
$o_inscricao->id_tipo_inscricao = $categoria_inscricao;
$o_inscricao->data_pagamento = $data_pagamento;
$o_inscricao->data_compensacao = $data_compensacao;
$o_inscricao->taxa = $taxa_pagamento;
$o_inscricao->data_registro = date("Y-m-d H:i:s");
$o_inscricao->quem_registrou = $_SESSION['logado']['login'];

if (!$o_inscricao->salva()) {
    $o_transacao->rollback();
	die("Atencao! Falha ao tentar gravar dados da inscricao de $nome_sem_acento: " . $o_inscricao->erro_sql);
} else {
	$o_individual = new IndividualDAO();
	$o_individual->id_inscricao = $o_inscricao->id;
	$o_individual->nome = $nome;
	$o_individual->email = $email;
	$o_individual->profissao = $profissao;
	$o_individual->cep = $o_empresa->cep;
	$o_individual->instituicao = $o_empresa->nome;
	$o_individual->situacao = 'A';
	
	if (!$o_individual->salva()) {
        $o_transacao->rollback();
        die("Atencao! Falha ao tentar gravar dados do usuario $nome_sem_acento: " . $o_individual->erro_sql);
	}
}

$o_transacao->commit();

$o_inscricao = new InscricaoDAO();
$a_funcionarios_inscritos = $o_inscricao->selecionar_funcionarios_inscritos($idEmpresa);
?>
<table width="100%" border="1" class="bordasimples">
	<tr style="font-weight: bold; text-align: center">
		<td>Inscrição</td>
		<td>Nome</td>
		<td>E-mail</td>
		<td>Profissão</td>
		<td>Tipo Insc.</td>
		<td>Operações</td>
	</tr>
	<?php foreach ($a_funcionarios_inscritos as $inscrito) { ?>
	<tr>
		<td align="center"><?php echo $inscrito->id ?></td>
		<td><?php echo trim(utf8_encode($inscrito->nome)) ?></td>
		<td><?php echo $inscrito->email ?></td>
		<td><?php echo utf8_encode($inscrito->profissao) ?></td>
		<td><?php echo utf8_encode($inscrito->descricao) ?></td>
		<td align="center"><input type='button' name='cancelar' id='cancelar' value='Cancelar' onclick='confirmaCancelamento(<?php echo $inscrito->id_individual ?>)' /></td>
	</tr>
	<?php } ?>
</table>