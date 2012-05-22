<?php
session_start();

require_once '../general/autoload.php';

header("Content-Type: application/xml; charset=utf-8");

$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$xml .= "<gravacao>\n";

if (empty($_SESSION['Funcionarios'])) {
	$xml .= "<erro>Voce deve adicionar pelo menos um funcionario na lista.</erro>";
	die($xml .= "</gravacao>");
}

$a_campos = array("email" => strtolower($_REQUEST['email']));
foreach($a_campos as $campo => $valor) {
	$o_empresa = new EmpresaDAO();

	if ($o_empresa->busca("$campo = '$valor'")) {
		$xml .= "<erro>O $campo informado ja encontra-se cadastrado em nosso sistema.</erro>";
        die($xml .= "</gravacao>");
	}
}

$id_tipo_inscricao = $_REQUEST['id_tipo_inscricao'];
$permito_divulgacao = $_REQUEST['permito_divulgacao'];

$o_transacao = new Banco();
$o_transacao->begin();

$o_empresa = new EmpresaDAO();
$o_empresa->nome = $_REQUEST['nome'];
$o_empresa->responsavel = $_REQUEST['responsavel'];
$o_empresa->email = strtolower($_REQUEST['email']);
$o_empresa->cep = $_REQUEST['cep'];

if (!$o_empresa->salva()) {
	$o_transacao->rollback();
	$xml .= "<erro>Falha ao tentar gravar dados da empresa: " . $o_empresa->erro_sql . "</erro>";
	die($xml .= "</gravacao>");
}

if (!empty($_SESSION['Funcionarios'])) {
	foreach ($_SESSION['Funcionarios'] as $funcionario) {
		$func_nome = $funcionario['func_nome'];
		$func_email = strtolower($funcionario['func_email']);
        $func_profissao = $funcionario['func_profissao'];

		$func_nome_sem_acento = Funcoes::remove_acentos($func_nome);

		$o_inscricao = new InscricaoDAO();
		$o_inscricao->id_empresa = $o_empresa->id;
		$o_inscricao->id_tipo_inscricao = $id_tipo_inscricao;

		if (!$o_inscricao->salva()) {
			$o_transacao->rollback();
			$xml .= "<erro>Falha ao tentar gravar dados da inscricao de $func_nome_sem_acento</erro>";
			die($xml .= "</gravacao>");
		}

		$o_individual = new IndividualDAO();
        $o_individual->id_inscricao = $o_inscricao->id;
        $o_individual->nome = $func_nome;
        $o_individual->email = $func_email;
        $o_individual->profissao = $func_profissao;
        $o_individual->cep = $o_empresa->cep;
        $o_individual->instituicao = $o_empresa->nome;
        $o_individual->situacao = 'A';
        $o_individual->permito_divulgacao = $permito_divulgacao;

		if (!$o_individual->salva()) {
			$o_transacao->rollback();
			$xml .= "<erro>Falha ao tentar gravar dados do usuario $func_nome_sem_acento</erro>";
			die($xml .= "</gravacao>");
		}
		
		EnviarEmail::enviar("cadastro", "individual", $func_email, $func_nome_sem_acento);
	}
}

$o_transacao->commit();

$nome_enviar = Funcoes::remove_acentos($o_empresa->nome);

$retorno = EnviarEmail::enviar("cadastro", "empresa", $o_empresa->email, $nome_enviar, $o_empresa->id);
if (!$retorno) {
	$xml .= "<erro>Falha ao tentar enviar e-mail para a empresa</erro>";
	die($xml .= "</gravacao>");
}

$mensagem = "Seus dados foram registrados em nosso sistema com sucesso. Agora voce devera efetuar o pagamento das inscricoes.";

$xml .= "<msg>$mensagem</msg>";
$xml .= "<id>" . $o_empresa->id . "</id>";
die($xml .= "</gravacao>");
?>
