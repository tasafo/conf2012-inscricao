<?php
	require_once 'relatorio.class.php';
	
	$opcao = $_REQUEST['opcao'];
	$__dataInicial = $_REQUEST['data_inicio'];
	$__dataFinal = $_REQUEST['data_fim'];
	$__tipo_inscricao = $_REQUEST['tipo_inscricao'];
	
	$relatorio = new Relatorio();

	switch ($opcao) {
		case "periodo":
			$relatorio->selecionarInscricoesPorPeriodo($__dataInicial, $__dataFinal);
			break;
		case "tipo_inscricao":
			$relatorio->selecionarInscricoesPorTipoInscricao($__tipo_inscricao);
			break;
		case "inscricoes_confirmadas":
			$relatorio->selecionarInscricoesComPagamentosConfirmados();
			break;
		case "inscricoes_confirmadas_periodo":
			$relatorio->selecionarInscricoesComPagamentosConfirmadosPorPeriodo($__dataInicial, $__dataFinal);
			break;
		case "ordem_alfabetica":
			$relatorio->selecionarInscricoesPorOdemAlfabetica();
			break;
		case "inscricoes_canceladas":
			$relatorio->selecionarInscricoesCanceladas();
			break;
		default:
			echo "Escolha uma opcao";
			break;
	}
?>