<?php
require_once dirname(__FILE__) . '/../general/autoload.php';

class InscricaoDAO extends AbstractDAO {
	public $id;
	public $id_empresa;
	public $id_tipo_inscricao;
	public $data_registro;
	public $data_pagamento;
	public $data_compensacao;
	public $tipo_pagamento;
	public $status_transacao;
	public $transacao_id;
	public $taxa;

	function __construct() {
		parent::__construct($this);
	}
	
    function inscritos_por_intervalo($inicio, $fim, $inadimplentes = false, $incluir_cancelados = false, $adimplentes = false, $presentes = false, $faltosos = false) {
        $filtro_inadimplentes = ($inadimplentes) ? "AND ins.data_pagamento IS NULL AND ins.id_empresa = 0" : "";
        
        $filtro_cancelados = ($incluir_cancelados) ? "" : "AND ind.situacao = 'A'";
        
        $filtro_adimplentes = ($adimplentes) ? "AND ins.data_pagamento IS NOT NULL" : "";
        
        $filtro_presentes = ($presentes) ? "AND ind.presente = 'S'" : "";
        
        $filtro_faltosos = ($faltosos) ? "AND ind.presente = 'N'" : "";
        
        $sql = "SELECT ind.* FROM individual ind
            JOIN inscricao ins ON (ind.id_inscricao = ins.id)
            WHERE ind.id BETWEEN $inicio AND $fim
            $filtro_inadimplentes
            $filtro_cancelados
            $filtro_adimplentes
            $filtro_presentes
            $filtro_faltosos
            ORDER BY ind.id";

        return $this->resultado_consulta($sql);
    }

    function total_de_pagamentos_por_compensacao() {
        $sql = "SELECT DATE(ins.data_compensacao) AS data_compensacao, SUM(tip.valor - ins.taxa) AS valor
            FROM inscricao ins
            JOIN tipo_inscricao tip ON (ins.id_tipo_inscricao = tip.id)
            JOIN individual ind ON (ins.id = ind.id_inscricao)
            WHERE ins.data_pagamento IS NOT NULL
            AND ind.situacao = 'A'
            GROUP BY DATE(ins.data_compensacao)
            HAVING SUM(tip.valor - ins.taxa) > 0
            ORDER BY DATE(ins.data_compensacao)";

        return $this->resultado_consulta($sql);
    }

    function total_de_inscritos_por_instituicao() {
        $sql = "SELECT instituicao, COUNT(*) AS quantidade
            FROM individual
            WHERE situacao = 'A'
            GROUP BY instituicao
            ORDER BY COUNT(*) DESC, instituicao";

        return $this->resultado_consulta($sql);
    }
    
    function valor_total_inscritos($situacao = null) {
        if ($situacao == "A") // Em aberto
            $condicao = " AND ins.data_pagamento IS NULL";
        else if ($situacao == "C") // Confirmadas
            $condicao = " AND ins.data_pagamento IS NOT NULL";
        else if ($situacao == "CR") // Confirmadas a Receber
            $condicao = " AND ins.data_pagamento IS NOT NULL AND ins.data_compensacao > '" . date("Y-m-d") . "'";
        else if ($situacao == "CD") // Confirmadas Disponivel
            $condicao = " AND ins.data_pagamento IS NOT NULL AND ins.data_compensacao <= '" . date("Y-m-d") . "'";
        else
            $condicao = "";
        
        $sql = "SELECT COUNT(*) AS quantidade, SUM(tip.valor - ins.taxa) AS valor
            FROM inscricao ins
            JOIN tipo_inscricao tip ON (ins.id_tipo_inscricao = tip.id)
            JOIN individual ind ON (ins.id = ind.id_inscricao)
            WHERE ind.situacao = 'A'
            $condicao";

        return $this->resultado_consulta($sql);
    }

	function selecionar_funcionarios_inscritos($id_empresa) {
		$sql = "SELECT ind.id AS id_individual, ind.nome, tip.valor, ind.email, tip.descricao, ins.id, ins.data_pagamento
            FROM individual ind
            JOIN inscricao ins ON (ind.id_inscricao = ins.id)
            JOIN tipo_inscricao tip ON (ins.id_tipo_inscricao = tip.id)
            WHERE ins.id_empresa = $id_empresa
            AND ind.situacao = 'A'
            ORDER BY ins.id";

		return $this->resultado_consulta($sql);
	}

	function selecionar_inscritos_individual($todos = false, $ordem = "ins.id DESC", $situacao = "A") {
        $situacao = $situacao == "A" ? "WHERE ind.situacao = 'A'" : "WHERE 1 = 1";
        
        $condicao = ($todos == false) ? " AND ins.id_empresa = 0" : "";
    
		$sql = "SELECT ins.id AS id_inscricao, ins.data_registro, ins.data_pagamento, ins.data_compensacao, ins.taxa,
            ind.nome, ind.email, ind.instituicao, tip.descricao AS descricao_tipo_inscricao, tip.valor,
            ind.id AS id_individual, ind.situacao, ind.presente
            FROM inscricao ins
            JOIN tipo_inscricao tip ON (ins.id_tipo_inscricao = tip.id)
            JOIN individual ind ON (ins.id = ind.id_inscricao)
            $situacao
            $condicao
            ORDER BY $ordem";

		return $this->resultado_consulta($sql);
	}

	function selecionar_inscritos_empresas() {
		$sql = "SELECT ins.id AS id_inscricao, ins.data_registro, ins.data_pagamento, ins.data_compensacao, ind.nome, ind.email,
            tip.descricao AS descricao_tipo_inscricao, tip.valor, emp.id AS id_empresa, emp.nome AS nome_empresa,
            emp.email AS email_empresa, emp.responsavel, ind.id AS id_individual, ins.taxa
            FROM inscricao ins
            JOIN tipo_inscricao tip ON (ins.id_tipo_inscricao = tip.id)
            JOIN individual ind ON (ins.id = ind.id_inscricao)
            JOIN empresa emp ON (ins.id_empresa = emp.id)
            WHERE ind.situacao = 'A'
            ORDER BY ins.id_empresa DESC, ins.id ASC";

		return $this->resultado_consulta($sql);
	}

	function consultar_qtd_inscricoes() {
		$sql = "SELECT COUNT(*) qtd
            FROM inscricao insc, individual ind, tipo_inscricao tip
			WHERE insc.id = ind.id_inscricao 
            AND tip.id = insc.id_tipo_inscricao
            AND ind.situacao = 'A'";

		return $this->resultado_consulta($sql);
	}

	function selecionar_inscricoes_periodo($dataInicial, $dataFinal) {
		$sql = "SELECT insc.id AS id_inscricao, insc.data_registro, ind.nome, insc.data_pagamento,
			tip.descricao AS descricao_tipo_inscricao, tip.valor
            FROM inscricao insc, individual ind, tipo_inscricao tip
			WHERE insc.id = ind.id_inscricao
			AND tip.id = insc.id_tipo_inscricao
            AND ind.situacao = 'A'
            AND date(insc.data_registro) >= '" . $dataInicial . "'
            AND date(insc.data_registro) <= '" . $dataFinal . "'
            ORDER BY insc.id";

		return $this->resultado_consulta($sql);
	}

	function selecionar_inscricoes_por_ordem_alfabetica() {
		$sql = "SELECT insc.id AS id_inscricao, insc.data_registro, ind.nome, insc.data_pagamento,
			tip.descricao AS descricao_tipo_inscricao, tip.valor
            FROM inscricao insc, individual ind, tipo_inscricao tip
			WHERE insc.id = ind.id_inscricao
            AND tip.id = insc.id_tipo_inscricao
            AND ind.situacao = 'A'
            ORDER BY trim(ind.nome)";

		return $this->resultado_consulta($sql);
	}

	function selecionar_inscricoes_com_pagamentos_confirmados() {
		$sql = "SELECT ind.nome, insc.id AS id_inscricao, insc.data_registro, insc.data_pagamento,
		    tip.descricao AS descricao_tipo_inscricao, tip.valor
            FROM individual ind, inscricao insc, tipo_inscricao tip
            WHERE insc.id = ind.id_inscricao
            AND insc.id_tipo_inscricao = tip.id
            AND insc.data_pagamento <> ''
            AND ind.situacao = 'A'
            ORDER BY trim(ind.nome)";

		return $this->resultado_consulta($sql);
	}

	function selecionar_inscricoes_com_pagamentos_confirmados_por_periodo_pagamento($dataInicial, $dataFinal) {
		$sql = "SELECT insc.id AS id_inscricao, insc.data_registro, ind.nome, insc.data_pagamento,
		    tip.descricao AS descricao_tipo_inscricao, tip.valor
            FROM inscricao insc, individual ind, tipo_inscricao tip
            WHERE insc.id = ind.id_inscricao
            AND insc.id_tipo_inscricao = tip.id
            AND ind.situacao = 'A'
            AND date(insc.data_pagamento) >= '" . $dataInicial . "'
            AND date(insc.data_pagamento) <= '" . $dataFinal . "'
            ORDER BY insc.id";

		return $this->resultado_consulta($sql);
	}

	function selecionar_inscricoes_por_tipo_inscricao($idTipoInscricao) {
		$sql = "SELECT insc.id AS id_inscricao, insc.data_registro, ind.nome, insc.data_pagamento,
		    tip.descricao AS descricao_tipo_inscricao, tip.valor 
            FROM inscricao insc, individual ind, tipo_inscricao tip
            WHERE insc.id = ind.id_inscricao
            AND insc.id_tipo_inscricao = tip.id
            AND insc.id_tipo_inscricao = ". $idTipoInscricao . "
            AND ind.situacao = 'A'
            ORDER BY trim(ind.nome)";

		return $this->resultado_consulta($sql);
	}

	function selecionar_inscricoes_canceladas() {
		$sql = "SELECT ind.nome, insc.id AS id_inscricao, insc.data_registro, insc.data_pagamento,
		    tip.descricao AS descricao_tipo_inscricao, tip.valor
            FROM inscricao insc, individual ind, tipo_inscricao tip
            WHERE insc.id = ind.id_inscricao
            AND insc.id_tipo_inscricao = tip.id
            AND ind.situacao = 'C'
            ORDER BY trim(ind.nome)";

		return $this->resultado_consulta($sql);
	}

	function selecionar_relacao_geral_inscritos($ordem = "nome") {
		$sql = "SELECT ind.id, ind.nome, tip.descricao AS descricao_tipo_inscricao, ind.instituicao
            FROM inscricao ins
            JOIN tipo_inscricao tip ON (ins.id_tipo_inscricao = tip.id)
            JOIN individual ind ON (ins.id = ind.id_inscricao)
            WHERE ind.situacao = 'A'
            AND ins.data_pagamento <> ''
            ORDER BY ind.$ordem";

		return $this->resultado_consulta($sql);
	}
}
?>