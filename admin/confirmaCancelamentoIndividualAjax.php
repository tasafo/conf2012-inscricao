<?php
require_once '../general/autoload.php';
require_once '../util/constantes.php';

header("Content-Type: application/xml; charset=utf-8");

$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$xml .= "<agilidade>\n";

$idIndividual = $_REQUEST['idIndividual'];

$o_individual = new IndividualDAO();
$o_individual->id = $idIndividual;
$o_individual->situacao = 'C';

if (!$o_individual->salva()) {
    $xml .= "<erro>Falha ao tentar cancelar a inscricao do usuario</erro>";
    $xml .= "<idIndividual>$idIndividual</idIndividual>";
	die($xml .= "</agilidade>");
}

$xml .= "<mensagem>Operacao realizada com sucesso. Recarregue a pagina para atualizar os valores</mensagem>";
$xml .= "<idIndividual>$idIndividual</idIndividual>";
die($xml .= "</agilidade>");
?>
