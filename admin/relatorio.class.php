<?php
require_once '../general/autoload.php';

class Relatorio {
  function templateRelatorio($__result) {
?>
          <table width="100%" style: border="1px solid">
              <tr style="font-weight: bold">
                  <td align="center">Inscri&ccedil;&atilde;o</td>
                  <td align="center">Data Inscri&ccedil;&atilde;o</td>
                  <td align="center">Nome</td>
                  <td align="center">Tipo de Inscri&ccedil;&atilde;o</td>
                  <td align="right">Valor</td>
                  <td align="center">Data Pagamento</td>
              </tr>
<?php

    $contador = 0;
    $valorInscricaoTotal = 0;
    $cor = "#FFFFFF";

    foreach ($__result as $individual) {
      $idInscricao = $individual->id_inscricao;
      $nome = $individual->nome;
      $dtRegistro = $individual->data_registro;
      $dtPagamento = $individual->data_pagamento;
      $dscTipoInscricao = $individual->descricao_tipo_inscricao;
      $valorInscricaoTotal += $individual->valor;

      $cor = ($cor == "#C5D6E8") ? "#FFFFFF" : "#C5D6E8";
?>
              <tr bgcolor="<?php echo $cor ?>" onMouseOver="this.style.background='#00559F'" onMouseOut="this.style.background='<?php echo $cor ?>'">
                  <td align="center"><?php echo $idInscricao ?></td>
                  <td align="center"><?php echo Funcoes::formata_data_para_exibir($dtRegistro) ?></td>
                  <td align="left"><?php echo utf8_encode($nome) ?></td>
                  <td align="left"><?php echo $dscTipoInscricao ?></td>
                  <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($individual->valor) ?></td>
                  <td align="center">
<?php 
      if ($dtPagamento == "") {
        echo "-";	
      } else {
        echo ($dtPagamento == "" ? "----" : Funcoes::formata_data_para_exibir($dtPagamento));
      }
?>
                  </td>
              </tr>
<?php
    }
?>
              <tr style="font-weight: bold; color: green">
                  <td colspan="4">N&uacute;mero de Registros: [ <?php echo sizeof($__result) ?> ]</td>
                  <td align="right"><?php echo Funcoes::formata_moeda_para_exibir($valorInscricaoTotal) ?></td>
                  <td>&nbsp;</td>
              </tr>
          </table>
<?php
  }

  function validarPeriodos($__dataInicial, $__dataFinal) {
    if (!Funcoes::checa_data($__dataInicial)) {
      echo "<h1>Por favor, digite uma data inicial válida</h1>";
      exit;
    }

    if (!Funcoes::checa_data($__dataFinal)) {
      echo "<h1>Por favor, digite uma data final válida</h1>";
      exit;
    }
  }

  function selecionarInscricoesPorPeriodo($__dataInicial, $__dataFinal) {
    $this->validarPeriodos($__dataInicial, $__dataFinal);

    $data_inicial = Funcoes::colocarDataFormatoMySQL($__dataInicial);
    $data_final = Funcoes::colocarDataFormatoMySQL($__dataFinal);

    $inscricaoDAO = new InscricaoDAO();
    $result = $inscricaoDAO->selecionar_inscricoes_periodo($data_inicial, $data_final);

    if (!$result) {
      echo "<h1>Nenhum registro encontrado</h1>";
      exit;
    } else {
?>
<h2><center>Inscritos por Per&iacute;odo: <?php echo $__dataInicial?> à <?php echo $__dataFinal?></center></h2>
<?php
      $this->templateRelatorio($result);
    }
  }

  function selecionarInscricoesPorOdemAlfabetica() {
    $inscricaoDAO = new InscricaoDAO();
    $result = $inscricaoDAO->selecionar_inscricoes_por_ordem_alfabetica();

    if (!$result) {
      echo "<h1>Nenhum registro encontrado</h1>";
      exit;
    } else {
?>
<h2><center>Lista de Inscritos em Ordem Alfab&eacute;tica</center></h2>
<?php
      $this->templateRelatorio($result);
    }
  }

  function selecionarInscricoesComPagamentosConfirmados() {
    $inscricaoDAO = new InscricaoDAO();
    $result = $inscricaoDAO->selecionar_inscricoes_com_pagamentos_confirmados();

    if (!$result) {
      echo "<h1>Nenhum registro encontrado</h1>";
      exit;
    } else {
?>
<h2><center>Lista de Inscri&ccedil;&otilde;es com Pagamentos Confirmados</center></h2>
<?php
      $this->templateRelatorio($result);
    }
  }

  function selecionarInscricoesComPagamentosConfirmadosPorPeriodo($__dataInicial, $__dataFinal) {
    $this->validarPeriodos($__dataInicial, $__dataFinal);

    $data_inicial = Funcoes::colocarDataFormatoMySQL($__dataInicial);
    $data_final = Funcoes::colocarDataFormatoMySQL($__dataFinal);

    $inscricaoDAO = new InscricaoDAO();
    $result = $inscricaoDAO->selecionar_inscricoes_com_pagamentos_confirmados_por_periodo_pagamento($data_inicial, $data_final);

    if (!$result) {
      echo "<h1>Nenhum registro encontrado</h1>";
      exit;
    } else {
?>
<h2><center>Pagamentos no Per&iacute;odo de: <?php echo $__dataInicial?> &agrave; <?php echo $__dataFinal?></center></h2>
<?php
      $this->templateRelatorio($result);
    }
  }

  function selecionarInscricoesPorTipoInscricao($__idTipoInscricao) {
    $inscricaoDAO = new InscricaoDAO();
    $result = $inscricaoDAO->selecionar_inscricoes_por_tipo_inscricao($__idTipoInscricao);

    if (!$result) {
      echo "<h1>Nenhum registro encontrado</h1>";
      exit;
    } else {
?>
<h2><center>Lista de Inscritos por Tipo de Inscri&ccedil;&atilde;o: <?php echo $__idTipoInscricao ?></center></h2>
<?php
      $this->templateRelatorio($result);
    }
  }

  function selecionarInscricoesCanceladas() {
    $inscricaoDAO = new InscricaoDAO();
    $result = $inscricaoDAO->selecionar_inscricoes_canceladas();

    if (!$result) {
      echo "<h1>Nenhum registro encontrado</h1>";
      exit;
    } else {
?>
<h2><center>Lista de Inscri&ccedil;&otilde;es Canceladas</center></h2>
<?php
      $this->templateRelatorio($result);
    }
  }
}
?>