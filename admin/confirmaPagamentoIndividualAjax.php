<?php
require_once '../general/autoload.php';

header("Content-Type: application/xml; charset=utf-8");

$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$xml .= "<agilidade>\n";

$idInscricao = $_REQUEST['idInscricao'];
$dtPagamento = $_REQUEST['dtPagamento'];
$dtCompensacao = $_REQUEST['dtCompensacao'];
$nome = $_REQUEST['nome'];
$email = $_REQUEST['email'];
$cortesia = $_REQUEST['cortesia'];

$txPagamento = 0;
if ($cortesia != "S")
    $txPagamento = Funcoes::formata_moeda_para_gravar($_REQUEST['txPagamento']);

if (!Funcoes::checa_data($dtPagamento)) {
    $xml .= "<erro>Data de pagamento invalida</erro>";
    $xml .= "<idInscricao>$idInscricao</idInscricao>";
    die($xml .= "</agilidade>");
}

if (!Funcoes::checa_data($dtCompensacao)) {
    $xml .= "<erro>Data de compensacao invalida</erro>";
    $xml .= "<idInscricao>$idInscricao</idInscricao>";
    die($xml .= "</agilidade>");
}

if (!is_numeric($txPagamento)) {
    $xml .= "<erro>Taxa invalida</erro>";
    $xml .= "<idInscricao>$idInscricao</idInscricao>";
    die($xml .= "</agilidade>");
}

$msg_recarregar = "";

if ($cortesia == "S") {
    $o_tipo_inscricao = new TipoInscricaoDAO();
    $a_tipo_inscricao = $o_tipo_inscricao->busca("descricao = 'Cortesia'");

    if (!$a_tipo_inscricao) {
        $xml .= "<erro>Tipo de Inscricao Cortesia nao foi encontrada</erro>";
        $xml .= "<idInscricao>$idInscricao</idInscricao>";
        die($xml .= "</agilidade>");
    }
    
    $o_inscricao = new InscricaoDAO();
    $o_inscricao->id = $idInscricao;
    $o_inscricao->id_tipo_inscricao = $a_tipo_inscricao[0]->id;

    if (!$o_inscricao->salva()) {
        $xml .= "<erro>Falha ao tentar atualizar o tipo de inscricao do usuario</erro>";
        $xml .= "<idInscricao>$idInscricao</idInscricao>";
        die($xml .= "</agilidade>");
    }

    $msg_recarregar = ". Recarregue a pagina para atualizar os valores";
}

$o_inscricao = new InscricaoDAO();
$o_inscricao->id = $idInscricao;
$o_inscricao->data_pagamento = Funcoes::formata_data_para_gravar($dtPagamento);
$o_inscricao->data_compensacao = Funcoes::formata_data_para_gravar($dtCompensacao);
$o_inscricao->taxa = $txPagamento;

if (!$o_inscricao->salva()) {
    $xml .= "<erro>Falha ao tentar atualizar o pagamento do usuario</erro>";
    $xml .= "<idInscricao>$idInscricao</idInscricao>";
    die($xml .= "</agilidade>");
}

$retorno = EnviarEmail::enviar("pagamento", "individual", $email, $nome);
if (!$retorno) {
    $xml .= "<erro>Falha ao tentar enviar e-mail para o usuario</erro>";
    $xml .= "<idInscricao>$idInscricao</idInscricao>";
    die($xml .= "</agilidade>");
}

$xml .= "<mensagem>Operacao realizada com sucesso. O E-mail ja foi enviado para o inscrito$msg_recarregar</mensagem>";
$xml .= "<dataPagamento>$dtPagamento</dataPagamento>";
$xml .= "<dataCompensacao>$dtCompensacao</dataCompensacao>";
$xml .= "<taxaPagamento>$txPagamento</taxaPagamento>";
$xml .= "<idInscricao>$idInscricao</idInscricao>";
die($xml .= "</agilidade>");
?>
