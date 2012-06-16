<?php
require 'validaSessao.php';
require_once '../general/autoload.php';

if ($_POST['inicio'] && $_POST['fim']) {
    $texto = $_POST['texto'];
    $inicio = $_POST['inicio'];
    $fim = $_POST['fim'];

    $so_adimplentes = false;
    if ($_POST['adimplentes'] && $_POST['adimplentes'] == "sim")
        $so_adimplentes = true;
    
    $so_inadimplentes = false;
    if ($_POST['inadimplentes'] && $_POST['inadimplentes'] == "sim")
        $so_inadimplentes = true;

    $incluir_membros_inadimplentes = false;
    if ($_POST['incluir_membros_inadimplentes'] && $_POST['incluir_membros_inadimplentes'] == "sim")
        $incluir_membros_inadimplentes = true;

    $so_presentes = false;
    if ($_POST['presentes'] && $_POST['presentes'] == "sim")
        $so_presentes = true;
        
    $so_faltosos = false;
    if ($_POST['faltosos'] && $_POST['faltosos'] == "sim")
        $so_faltosos = true;
        
    $incluir_cancelados = false;
    if ($_POST['cancelados'] && $_POST['cancelados'] == "sim")
        $incluir_cancelados = true;
    
    $o_inscritos = new InscricaoDAO();
    $a_inscritos = $o_inscritos->inscritos_por_intervalo($inicio, $fim, $so_inadimplentes, $incluir_cancelados, $so_adimplentes, $so_presentes, $so_faltosos, $incluir_membros_inadimplentes);
    
    if ($a_inscritos) {
        echo "<h2>Log de envio de e-mail's</h2></center>";
        
        foreach($a_inscritos as $inscrito) {
            $id = $inscrito->id;
            $nome = $inscrito->nome;
            $email = $inscrito->email;
            
            $retorno = EnviarEmail::enviar("aviso", "individual", $email, $nome, $id, $texto);
            if (!$retorno)
                echo "$id - O e-mail para <b>$email</b> nao foi enviado<br>";
            else
                echo "$id - O e-mail para <b>$email</b> foi enviado com sucesso<br>";
        }
    } else {
        die("<h2>Nenhuma inscri&ccedil;&atilde;o encontrada</h2></center>");
    }
} else {
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
        <title>Envio de email's</title>
        <link href="css/admin.css" rel="stylesheet" />
    </head>
    <body>
        <center>
            <h2>Envio de email's</h2>
        </center>
        <form action="" method="post">
            <fieldset>
                <legend><b>Texto</b> (<i>se ficar vazio, irá enviar o texto padrão de cobrança</i>)</legend>
                <textarea rows="15" cols="80" name="texto"></textarea>
            </fieldset>
            <br>
            <fieldset>
                <legend><b>Enviar somente para</b></legend>
                <input type="checkbox" name="adimplentes" id="adimplentes" value="sim" />Os que confirmaram sua inscrição<br><br>
                <input type="checkbox" name="inadimplentes" id="inadimplentes" value="sim" />Os que estão com inscrição em aberto - 
                <input type="checkbox" name="incluir_membros_inadimplentes" id="incluir_membros_inadimplentes" value="sim" /><i>Incluir os membros da empresa / instituição</i><br><br>
                <input type="checkbox" name="presentes" id="presentes" value="sim" />Os presentes no evento<br><br>
                <input type="checkbox" name="faltosos" id="faltosos" value="sim" />Os faltosos no evento<br><br>
                <input type="checkbox" name="cancelados" id="cancelados" value="sim" />Incluir os cancelados<br>
            </fieldset>
            <br>
            <fieldset>
                <legend><b>Informe um intervalo baseado no id da pessoa</b></legend>
                Inicio: <input type="text" size="5" name="inicio" id="inicio" value=""><br><br>
                Fim: <input type="text" size="5" name="fim" id="fim" value=""><br>
            </fieldset><br>
            <input type="submit" name="submit" value="ENVIAR">
        </form>
    </body>
</html>
<?php
}
?>