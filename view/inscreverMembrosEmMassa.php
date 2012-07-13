<?php
require '../admin/validaSessao.php';
require_once '../general/autoload.php';

$o_tipo_inscricao = new TipoInscricaoDAO();

$a_tipo_inscricao = $o_tipo_inscricao->busca();

$select_tipo_inscricao = "";
foreach ($a_tipo_inscricao as $tipo_inscricao) {
    $select_tipo_inscricao .= "<option value='" . $tipo_inscricao->id . "'>" . $tipo_inscricao->descricao . " - R$ ". Funcoes::formata_moeda_para_exibir($tipo_inscricao->valor) . "</option>";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <title>Inscrição em massa</title>
        <script type="text/javascript" src="js/jquery/jquery.js"></script>
        <script type="text/javascript" src="js/jquery/jquery.validate.js"></script>
        <script type="text/javascript" src="js/validacao.js"></script>
        <script type="text/javascript" src="js/empresa.js"></script>
        <link type="text/css" href="css/validacao.css" rel="stylesheet" />
    </head>
    <body>
    <?php
    if (!$_FILES['arquivo']) {
    ?>
        <center>
            <h2>Passo 1 - Importar membros</h2>
            <b>Padrão do arquivo .csv:</b> "Nome","E-mail","Profissão"<br><br>
            <form name="frmMembros" method="post" enctype="multipart/form-data" action="inscreverMembrosEmMassa.php">
                Arquivo: <input type="file" size="40" name="arquivo"><br><br>
                <input type="submit" id="arquivo" name="arquivo" value="Importar membros" />
            </form>
        </center>
    <?php
    } else {
        if ($_FILES['arquivo']['error'] == 0) {
            $arquivo_temp = $_FILES['arquivo']['tmp_name'];
            $nome_arquivo = $_FILES['arquivo']['name'];
            $diretorio = dirname(__FILE__) . "/../temp";
            $arquivo_copiado = "$diretorio/$nome_arquivo";

            if (!move_uploaded_file($arquivo_temp, $arquivo_copiado)) {
                echo "<h2>$msg_erro - Não foi possível importar o arquivo de membros</h2>";
            } else {
                $_SESSION['Funcionarios'] = array();
                
                $csv = fopen($arquivo_copiado, "r");
                
                $o_individual = new IndividualDAO();
                
                $contador_existe = 0;
                
                while (!feof($csv)) {
                    $linha = fgets($csv, 4096);
                    $linha = str_replace('"', '', $linha);
                    
                    $registro = explode(',', $linha);
                    
                    $nome = $registro[0];
                    $email = $registro[1];
                    $profissao = $registro[2];

                    if (trim($nome) != "") {
                        $existe = "NÃO";
                        if ($o_individual->busca("email = '$email'")) {
                            $exite = "SIM";
                            $contador_existe++;
                        }

                        $a_funcionario = array(
                            'func_nome' => $nome,
                            'func_email' => $email,
                            'func_profissao' => $profissao,
                            'existe' => $existe
                        );                    

                        $_SESSION['Funcionarios'][] = $a_funcionario;
                    }
                }
                fclose($csv);
                ?>
                <center><h2>Passo 2 - Inscrição de membros em massa</h2></center>
                <div class="container" id="div_msg_empresa">
                    <ol></ol>
                </div>
                <br>
                <form id="form" name="formMassa" method="post" action="">
                    <table>
                        <tr>
                            <td align="left" width="40%">Pagamento</td>
                            <td align="left" width="60%">
                                <select name="id_tipo_inscricao" id="id_tipo_inscricao" style="width: 340px">
                                <?php echo $select_tipo_inscricao ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td align="left">Nome da Instituição</td>
                            <td align="left">
                              <input type="text" name="nome" id="nome" maxlength="60" class="caixa" size="35" />
                            </td>
                        </tr>
                        <tr>
                            <td align="left">Nome do Responsável</td>
                            <td align="left">
                              <input type="text" name="responsavel" id="responsavel" class="caixa" maxlength="60" size="35" />
                            </td>
                        </tr>
                        <tr>
                            <td align="left">E-mail</td>
                            <td align="left">
                              <input type="text" name="email" id="email" class="caixa" maxlength="45" size="35" />
                            </td>
                        </tr>
                        <tr>
                            <td align="left">CEP</td>
                            <td align="left">
                              <input type="text" name="cep" id="cep" class="caixa" maxlength="8" size="9" onKeyPress="mascara(this,apenasNumeros);" /> ( somente números )
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <div id="div_botao_salvar">
                                    <?php if ($contador_existe == 0) { ?>
                                        <input type="button" id="gravar" class="submit" value="Realizar Inscrições" />
                                    <?php } else { ?>
                                        <span style="color: red">Verifique os e-mails já existentes e tente novamente</span>
                                    <?php } ?>
                                </div>
                                <div id="div_salvando" style="color: red"></div>
                            </td>
                        </tr>
                    </table>
                </form>
                
                <table border="1" cellpadding="1" cellspacing="1" width="100%">
                    <tr align="center" style="font-weight: bold">
                        <td width="05%">N.</td>
                        <td width="35%">Nome</td>
                        <td width="25%">E-mail</td>
                        <td width="25%">Profissão</td>
                        <td width="10%">Já existe</td>
                    </tr>
                    <?php foreach ($_SESSION['Funcionarios'] as $key => $funcionario) { ?>
                    <tr>
                        <td align="center"><?php echo ++$item ?></td>
                        <td align="left"><?php echo $funcionario['func_nome'] ?></td>
                        <td align="left"><?php echo $funcionario['func_email'] ?></td>
                        <td align="left"><?php echo $funcionario['func_profissao'] ?></td>
                        <td align="center"><?php echo $funcionario['existe'] ?></td>
                    </tr>
                    <?php } ?>
                  </table>
                </table>
     <?php
            }
        }
     }
     ?>
    </body>
</html>