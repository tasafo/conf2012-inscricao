<?php
require_once '../general/autoload.php';

$o_empresa = new EmpresaDAO();
$o_individual = new IndividualDAO();

$email = trim(strtolower($_POST['email']));

header("Content-Type: application/xml; charset=utf-8");

$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$xml .= "<recupera>\n";

if (empty($email)) {
    $xml .= "<erro>Informacoes incorretas.</erro>";
    die($xml .= "</recupera>");
}

$a_empresa = $o_empresa->busca("email = '$email'");
if ($a_empresa) {
    $id = $a_empresa[0]->id;
    $xml .= "<id>$id</id>";
    $xml .= "<tipo>Empresa</tipo>";
} else {
    $a_individual = $o_individual->busca("email = '$email' AND situacao = 'A'");
    if ($a_individual) {
        $id = $a_individual[0]->id;
        $xml .= "<id>$id</id>";
        $xml .= "<tipo>Individual</tipo>";
    } else {
        $xml .= "<erro>E-mail nao encontrado.</erro>";
        die($xml .= "</recupera>");
    }
}
die($xml .= "</recupera>");
?>