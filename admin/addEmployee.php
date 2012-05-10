<?php
require_once '../general/autoload.php';

$idEmpresa = $_REQUEST['id'];

$o_empresa = new EmpresaDAO();
$o_inscricao = new InscricaoDAO();
$o_tipo_inscricao = new TipoInscricaoDAO();

$a_tipo_inscricao = $o_tipo_inscricao->busca();

$select = "";
foreach ($a_tipo_inscricao as $tipo_inscricao) {
	$select .= "<option value='" . $tipo_inscricao->id . "'>" . $tipo_inscricao->descricao . " - R$ " . Funcoes::formata_moeda_para_exibir($tipo_inscricao->valor) . "</option>";
}

$o_empresa->busca($idEmpresa);

$a_funcionarios_inscritos = $o_inscricao->selecionar_funcionarios_inscritos($idEmpresa);
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
        <title>Adicionar Membros</title>
        <script type="text/javascript" src="../view/js/validacao.js" ></script>
        <script type="text/javascript" src="../view/js/jquery/jquery.js" ></script>
        <script type="text/javascript" src="../view/js/jquery/jquery.validate.js" ></script>
        <script type="text/javascript" src="../view/js/jquery/jquery.alerts/jquery.alerts.js" ></script>
        <script type="text/javascript" src="js/employee.js" ></script>
        <link href="../view/js/jquery/jquery.alerts/jquery.alerts.css" rel="stylesheet" />
        <link type="text/css" href="../view/css/validacao.css" rel="stylesheet" />
  	 </head>
	<body>
        <form class="cmxform" id="frmFunc" name="formFuncionarios" action="" method="post">
            <input type="hidden" name="hdnIdEmpresa" id="hdnIdEmpresa" value="<?php echo $idEmpresa ?>" />
            <table class="bordasimples" style="width: 750px">
                <tr>
                    <td colspan="2" align="center"><a href="relatorioEmpresas.php">Voltar para o relatório de empresas</a></td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><b>Adicionar Membros da Instituição: <?php echo utf8_encode($o_empresa->nome) ?></b></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="container" id="div_msg_funcionario">
                            <ol>
                            </ol>
                        </div>
                    </td>
                </tr>
				<tr>
					<td align="right" width="20%">Forma Pagto.</td>
					<td align="left" width="80%">
						<select name="func_categoria_inscricao" id="func_categoria_inscricao" style="width: 340px">
							<?php echo $select ?>
						</select>
					</td>
				</tr>
				<tr>
					<td align="right">Nome</td>
                    <td align="left"><input type="text" name="func_nome" id="func_nome" maxlength="60" size="35"/></td>
				</tr>
				<tr>
					<td align="right">E-mail</td>
					<td align="left"><input type="text" name="func_email" id="func_email" maxlength="50" size="35"/></td>
				</tr>
                <tr>
                    <td align="right">Profissão</td>
                    <td align="left"><input type="text" name="func_profissao" id="func_profissao" maxlength="50" size="35"/></td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><input type="button" id="insere_funcionario" value="Cadastrar Funcionário" /></td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div id="div_grade_funcionarios">
                            <table width="100%" border="1">
                                <tr style="font-weight: bold; text-align: center">
                                    <td>Inscrição</td>
                                    <td>Nome</td>
                                    <td>E-mail</td>
                                    <td>Tipo Insc.</td>
                                    <td>Operações</td>
                                </tr>
                                <?php foreach ($a_funcionarios_inscritos as $inscrito) { ?>
                                <tr id="row_<?php echo $inscrito->id_individual ?>">
                                    <td align="center"><?php echo $inscrito->id ?></td>
                                    <td><?php echo trim(utf8_encode($inscrito->nome)) ?></td>
                                    <td><?php echo $inscrito->email ?></td>
                                    <td><?php echo $inscrito->descricao ?></td>
                                    <td align="center"><input type='button' name='cancelar' id='cancelar' value='Cancelar' onclick='confirmaCancelamento(<?php echo $inscrito->id_individual ?>)' /></td>
                                </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
		</form>
	</body>
</html>