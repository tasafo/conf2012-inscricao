<?php
session_start();

require_once '../general/autoload.php';

$acao = $_REQUEST['acao'];

$id_tipo_inscricao = $_REQUEST['id_tipo_inscricao'];

$o_tipo_inscricao = new TipoInscricaoDAO();

if (!$o_tipo_inscricao->busca($id_tipo_inscricao))
    die("Atencao! Nao foi encontrado o tipo de inscricao no sistema.");

if ($acao == 'incluir') {
	$nome = $_REQUEST['func_nome'];
	$email = $_REQUEST['func_email'];
	$profissao = $_REQUEST['func_profissao'];
            
	if (!empty($_SESSION['Funcionarios'])) {
		foreach ($_SESSION['Funcionarios'] as $key => $funcionario) {
			if ($nome == $funcionario['func_nome'])
				die("Atencao! O Nome ja esta lancado na lista");

			if ($email == $funcionario['func_email'])
				die("Atencao! O E-mail ja esta lancado na lista");
		}
	}

	$a_campos = array("email");
	foreach($a_campos as $campo) {
		$o_individual = new IndividualDAO();

		if ($o_individual->busca("$campo = '" . $$campo . "'"))
			die("Atencao! Este $campo ja foi utilizando em uma inscricao no sistema.");
	}

	$a_funcionario = array(
        'func_nome' => $nome,
        'func_email' => $email,
        'func_profissao' => $profissao
	);

	$_SESSION['Funcionarios'][] = $a_funcionario;

} elseif ($acao == 'excluir') {
	$codigo = $_REQUEST['codigo'];

	unset($_SESSION['Funcionarios'][$codigo]);
}

if (!empty($_SESSION['Funcionarios'])) {
?>
<table border="1" cellpadding="1" cellspacing="1" width="100%">
    <tr>
        <td align="center" colspan="4">
            <?php
            $numero_inscritos = count($_SESSION['Funcionarios']);
            $valor_inscricao = $o_tipo_inscricao->valor;
            $valor_total_inscritos = $numero_inscritos * $valor_inscricao;
            echo "<b>R$ " . Funcoes::formata_moeda_para_exibir($valor_inscricao) . " x $numero_inscritos inscrito(s) no total de R$ " . Funcoes::formata_moeda_para_exibir($valor_total_inscritos)."</b>";
            ?>
        </td>
    </tr>
	<tr>
		<td width="05%" align="center"><b>N.</b></td>
		<td width="50%" align="left"><b>Nome</b></td>
		<td width="50%" align="left"><b>E-mail</b></td>
		<td width="05%" align="center"><b>Excluir</b></td>
	</tr>
	<?php
	$item = 1;
	foreach ($_SESSION['Funcionarios'] as $key => $funcionario) {
	?>
	<tr>
		<td align="center"><?php echo $item++ ?></td>
		<td align="left"><?php echo $funcionario['func_nome'] ?></td>
		<td align="left"><?php echo $funcionario['func_email'] ?></td>
		<td align="center"><a
			onclick="atualizaFuncionarioAjax(<?php echo $key ?>, 'excluir')"><img
			src="images/excluir.gif" /></a></td>
	</tr>
	<?php
	}
	?>
</table>
<?php
}
?>