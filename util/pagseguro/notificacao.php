<?php
require_once '../../general/autoload.php';
require_once '../constantes.php';

if (count($_POST) > 0) {
    $notification_type = $_POST['notificationType'];
    $notification_code = $_POST['notificationCode'];
    
    $a_tipos_pagamento = array(
        1 => "Cartao de Credito",
        2 => "Boleto",
        3 => "Debito online (TEF)",
        4 => "Saldo PagSeguro",
        5 => "Oi Paggo"
    );
    
    $a_status_transacao = array(
        1 => "Aguardando pagamento",
        2 => "Em analise",
        3 => "Paga",
        4 => "Disponivel",
        5 => "Em disputa",
        6 => "Devolvida",
        7 => "Cancelada"
    );

    if ($notification_type == 'transaction' AND !empty($notification_code)) {
        $url_notificacao = "https://ws.pagseguro.uol.com.br/v2/transactions/notifications/$notification_code?email=" . EMAIL_COBRANCA . "&token=" . TOKEN; 

        $xml = simplexml_load_file($url_notificacao);
        
        if ($xml) {
            if ($xml->type == "1" && $xml->status == "3") { // type = 1-Pagamento, status = 3-Paga
                $transaction_code = $xml->code;
                
                $modo_pagamento = substr($xml->reference, 0, 1);
                $id_ref_pagamento = substr($xml->reference, 1);

                $data_criacao_transacao = substr($xml->date, 0, 10) . " " . substr($xml->date, 12);
                
                $data_pagamento = date("Y-m-d H:i:s");
                
                $a_data_pag = explode('-', substr($xml->date, 0, 10));
                
                $data_compensacao = date('Y-m-d', strtotime("+14 days", strtotime($a_data_pag[2] . "-" . $a_data_pag[1] . "-" . $a_data_pag[0])));
                                                    
                $valor_taxa = (float)$xml->feeAmount;
      
                $tipo_pagamento = $a_tipos_pagamento[(int)$xml->paymentMethod->type];
                
                $status_transacao = $a_status_transacao[(int)$xml->status];
                
                if ($modo_pagamento == "I") { // Individual
                    $id_individual = $id_ref_pagamento;
                    
                    $o_individual = new IndividualDAO();
                    
                    if ($o_individual->busca($id_individual)) {
                        $nome = $o_individual->nome;
                        $email = $o_individual->email;
                        
                        $o_inscricao = new InscricaoDAO();
                        
                        if ($o_inscricao->busca($o_individual->id_inscricao)) {
                            $id_inscricao = $o_inscricao->id;
                                    
                            $o_inscricao = new InscricaoDAO();
                            $o_inscricao->id = $id_inscricao;
                            $o_inscricao->data_criacao_transacao = $data_criacao_transacao;
                            $o_inscricao->data_pagamento = $data_pagamento;
                            $o_inscricao->data_compensacao = $data_compensacao;
                            $o_inscricao->taxa = $valor_taxa;
                            $o_inscricao->tipo_pagamento = $tipo_pagamento;
                            $o_inscricao->status_transacao = $status_transacao;
                            $o_inscricao->transacao_id = $transaction_code;

                            if ($o_inscricao->salva())
                                EnviarEmail::enviar("pagamento", "individual", $email, $nome);
                        }
                    }
                } elseif ($modo_pagamento == "E") { // Empresa
                    $id_empresa = $id_ref_pagamento;
                    
                    $o_empresa = new EmpresaDAO();
                    
                    if ($o_empresa->busca($id_empresa)) {
                        $nome = $o_empresa->nome;
                        $email = $o_empresa->email;
                    
                        $o_inscricao = new InscricaoDAO();
                        $a_funcionarios_empresa = $o_inscricao->selecionar_funcionarios_inscritos($id_empresa);

                        if ($a_funcionarios_empresa) {
                            $total_funcionarios = count($a_funcionarios_empresa);

                            $taxa_por_pessoa = ($valor_taxa > 0) ? round($valor_taxa / $total_funcionarios, 2) : 0;

                            $sobra = round($total_funcionarios * $taxa_por_pessoa - $valor_taxa, 2);

                            $lista_funcionarios = "";
                            $contador = 0;

                            foreach ($a_funcionarios_empresa as $inscrito) {
                                $contador++;
                                
                                $nome_func = Funcoes::remove_acentos(utf8_encode($inscrito->nome));
                                $email_func = $inscrito->email;
                                
                                $taxa = ($contador == $total_funcionarios) ? $taxa_por_pessoa - $sobra : $taxa_por_pessoa;
                                
                                $lista_funcionarios .= "$nome_func - $email_func<br><br>";

                                $o_inscricao = new InscricaoDAO();
                                $o_inscricao->id = $inscrito->id;
                                $o_inscricao->data_criacao_transacao = $data_criacao_transacao;
                                $o_inscricao->data_pagamento = $data_pagamento;
                                $o_inscricao->data_compensacao = $data_compensacao;
                                $o_inscricao->taxa = $taxa;
                                $o_inscricao->tipo_pagamento = $tipo_pagamento;
                                $o_inscricao->status_transacao = $status_transacao;
                                $o_inscricao->transacao_id = $transaction_code;

                                if ($o_inscricao->salva())
                                    EnviarEmail::enviar("pagamento", "individual", $email_func, $nome_func);
                            }
                            
                            EnviarEmail::enviar("pagamento", "empresa", $email, $nome, 0, $lista_funcionarios);
                        }
                    }
                }
            }
        }
    }
}
?>