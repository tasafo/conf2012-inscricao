<?php
session_start();
require_once '../general/autoload.php';

header("Content-Type: application/xml; charset=utf-8");

$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$xml .= "<gravacao>\n";
 
if (!$_POST['id']) {
    $xml .= "<erro>Selecione ao menos um inscrito.</erro>";
    die($xml .= "</gravacao>");
}

$o_transacao = new Banco();
$o_transacao->begin();

foreach($_POST['id'] as $codigo) {
    $o_individual = new IndividualDAO();
    
    if ($o_individual->busca($codigo)) {
        $o_individual = new IndividualDAO();
        $o_individual->id = $codigo;
        $o_individual->presente = "S";
        $o_individual->quem_registrou_presenca = $_SESSION['logado']['login'];
    
        if (!$o_individual->salva()) {
            $o_transacao->rollback();
            $xml .= "<erro>Falha ao tentar marcar presenca do inscrito</erro>";
            die($xml .= "</gravacao>");
        }
    }
}

$o_transacao->commit();

$xml .= "<msg>Presenca concluida com sucesso</msg>";
die($xml .= "</gravacao>");
?>