<?php
require 'validaSessao.php';
require_once '../general/autoload.php';
require_once '../util/constantes.php';

$msg_ok = "<font color='blue'><b>[Ok]</b></font>";
$msg_aviso = "<font color='green'><b>[Aviso]</b></font>";
$msg_erro = "<font color='red'><b>[Erro]</b></font>";

if ($_FILES['arquivo']) {
    if ($_FILES['arquivo']['error'] == 0) {
        $arquivo_temp = $_FILES['arquivo']['tmp_name'];
        $nome_arquivo = $_FILES['arquivo']['name'];
        $diretorio = dirname(__FILE__) . "/pagtoimport";
        $arquivo_copiado = "$diretorio/$nome_arquivo";

        if (!move_uploaded_file($arquivo_temp, $arquivo_copiado)) {
            echo "<h2>$msg_erro - Não foi possível importar o arquivo de pagamentos</h2>";
        } else {
            $xml = simplexml_load_file($arquivo_copiado);
            
            if (!$xml) {
                echo "<h2>$msg_erro - O arquivo de pagamentos não é um XML válido</h2>";
            } else {
                if (!$xml->Table) {
                    echo "<h2>$msg_erro - A estrutura do arquivo de pagamentos não é válida</h2>";
                } else {
                    echo "<b>Log de importação de pagamentos</b><br><br>";
                
                    foreach($xml->Table as $pagamento) {
                        if ($pagamento->Tipo_Transacao == "Pagamento" && $pagamento->Status == "Aprovada") {
                            echo "<b> > " . $pagamento->Cliente_Nome . "</b> ";
                            
                            $modo_pagamento = substr($pagamento->Ref_Transacao, 0, 1);
                            $id_ref_pagamento = substr($pagamento->Ref_Transacao, 1);
                            $data_pagamento = Funcoes::formata_data_para_gravar(substr($pagamento->Data_Transacao, 0, 10)) . substr($pagamento->Data_Transacao, 10);
                            $data_compensacao = Funcoes::formata_data_para_gravar(substr($pagamento->Data_Compensacao, 0, 10)) . substr($pagamento->Data_Compensacao, 10);
                            $valor_taxa = (float)Funcoes::formata_moeda_para_gravar($pagamento->Valor_Taxa);
                            
                            if ($modo_pagamento == "I") { // Individual
                                $id_individual = $id_ref_pagamento;
                                
                                $o_individual = new IndividualDAO();
                                
                                if (!$o_individual->busca($id_individual)) {
                                    echo "$msg_erro - Usuário não encontrado<br><br>";
                                } else {
                                    $nome = $o_individual->nome;
                                    $email = $o_individual->email;
                                    
                                    $o_inscricao = new InscricaoDAO();
                                    
                                    if (!$o_inscricao->busca($o_individual->id_inscricao)) {
                                        echo "$msg_erro - Inscrição não encontrada<br><br>";
                                    } else {
                                        if (!empty($o_inscricao->data_pagamento)) {
                                            echo "$msg_aviso - O pagamento já consta no sistema<br><br>";
                                        } else {
                                            $id_inscricao = $o_inscricao->id;
                                            
                                            $o_inscricao = new InscricaoDAO();
                                            $o_inscricao->id = $id_inscricao;
                                            $o_inscricao->data_pagamento = $data_pagamento;
                                            $o_inscricao->data_compensacao = $data_compensacao;
                                            $o_inscricao->taxa = $valor_taxa;
                                            $o_inscricao->tipo_pagamento = Funcoes::remove_acentos($pagamento->Tipo_Pagamento);
                                            $o_inscricao->status_transacao = Funcoes::remove_acentos($pagamento->Status);
                                            $o_inscricao->transacao_id = $pagamento->Transacao_ID;

                                            if (!$o_inscricao->salva()) {
                                                echo "$msg_erro - Falha ao tentar atualizar o pagamento do usuario<br><br>";
                                            } else {
                                                $retorno = EnviarEmail::enviar("pagamento", "individual", $email, $nome);
                                                if (!$retorno)
                                                    echo "$msg_erro - Falha ao tentar enviar e-mail para o usuario<br><br>";
                                                else
                                                    echo "$msg_ok<br><br>";
                                            }
                                        }
                                    }
                                }
                            } elseif ($modo_pagamento == "E") { // Empresa
                                $id_empresa = $id_ref_pagamento;
                                
                                $o_empresa = new EmpresaDAO();
                                
                                if (!$o_empresa->busca($id_empresa)) {
                                    echo "$msg_erro - Empresa não encontrada<br><br>";
                                } else {
                                    $nome = $o_empresa->nome;
                                    $email = $o_empresa->email;
                                
                                    $o_inscricao = new InscricaoDAO();
                                    $a_funcionarios_empresa = $o_inscricao->selecionar_funcionarios_inscritos($id_empresa);

                                    if (!$a_funcionarios_empresa) {
                                        echo "$msg_erro - Nao foi encontrado nenhum funcionario da empresa<br><br>";
                                    } else {
                                        $total_funcionarios = count($a_funcionarios_empresa);

                                        $taxa_por_pessoa = ($valor_taxa > 0) ? round($valor_taxa / $total_funcionarios, 2) : 0;

                                        $sobra = round(($total_funcionarios * $taxa_por_pessoa) - $valor_taxa, 2);

                                        $lista_funcionarios = "";
                                        $contador = 0;

                                        foreach ($a_funcionarios_empresa as $inscrito) {
                                            $contador++;
                                            
                                            $nome_func = Funcoes::remove_acentos(utf8_encode($inscrito->nome));
                                            $email_func = $inscrito->email;
                                            
                                            $valor_taxa = ($contador == $total_funcionarios) ? $taxa_por_pessoa - $sobra : $taxa_por_pessoa;
                                            
                                            $lista_funcionarios .= "$nome_func - $email_func<br><br>";

                                            if (!empty($inscrito->data_pagamento)) {
                                                echo "<br>$msg_aviso - O pagamento da inscrição de $nome_func já consta no sistema";
                                            } else {
                                                $o_inscricao = new InscricaoDAO();
                                                $o_inscricao->id = $inscrito->id;
                                                $o_inscricao->data_pagamento = $data_pagamento;
                                                $o_inscricao->data_compensacao = $data_compensacao;
                                                $o_inscricao->taxa = $valor_taxa;
                                                $o_inscricao->tipo_pagamento = Funcoes::remove_acentos($pagamento->Tipo_Pagamento);
                                                $o_inscricao->status_transacao = Funcoes::remove_acentos($pagamento->Status);
                                                $o_inscricao->transacao_id = $pagamento->Transacao_ID;

                                                if (!$o_inscricao->salva()) {
                                                    echo "<br>$msg_erro - Falha ao tentar atualizar o pagamento de $nome_func";
                                                } else {
                                                    $retorno = EnviarEmail::enviar("pagamento", "individual", $email_func, $nome_func);
                                            
                                                    if (!$retorno)
                                                        echo "<br>$msg_erro - Falha ao tentar enviar e-mail para $nome_func";
                                                    else
                                                        echo "<br>$nome_func - $msg_ok<br><br>";
                                                }
                                            }
                                        }
                                        
                                        $retorno = EnviarEmail::enviar("pagamento", "empresa", $email, $nome, 0, $lista_funcionarios);
                                        if (!$retorno)
                                            echo "<br>$msg_erro - Falha ao tentar enviar e-mail para a empresa<br><br>";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <title>Importar Pagamentos</title>
    </head>
    <body>
        <center>
            <h3><a href="menu.php">Voltar ao Menu</a></h3>
            <h2>Importar Pagamentos</h2>
            <form name="frmDirf" method="post" enctype="multipart/form-data" action="importarPagamentos.php">
                Arquivo: <input type="file" size="40" name="arquivo"><br><br>
                <input type="submit" id="arquivo" name="arquivo" value="Importar arquivo" />
            </form>
        </center>
    </body>
</html> 
