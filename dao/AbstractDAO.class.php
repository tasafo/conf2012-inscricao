<?php
require_once dirname(__FILE__) . '/../general/autoload.php';
require_once dirname(__FILE__) . '/../util/constantes.php';

class AbstractDAO {
	protected $conexao;
	protected $banco;
	protected $tabela;
	protected $classe;
	protected $campos;
	public $erro_sql;

	protected function __construct($classe) {
		$this->banco = new Banco();
		$this->classe = get_class($classe);
		$this->tabela = $this->converte_nome_da_classe_para_tabela($this->classe);
		$this->atualiza_atributos();
	}
	
	private function exibe_sql($sql) {
		if (EXIBIR_SQL) echo "<pre><b>$sql<b></pre>";
	}

	private function atualiza_atributos() {
		$this->campos = get_object_vars($this);
		$this->remove_campos_desnecessarios();
	}

	private function remove_campos_desnecessarios() {
		unset($this->campos['banco']);
		unset($this->campos['conexao']);
		unset($this->campos['tabela']);
		unset($this->campos['classe']);
		unset($this->campos['campos']);
	}

	private function converte_nome_da_classe_para_tabela($classe) {
		$classe = str_replace("DAO", "", $classe);
		$classe = strtolower($classe[0]) . substr($classe, 1);

		$nome_tabela = "";

		for ($i = 0; $i < strlen($classe); $i++) {
			$caractere = $classe[$i];

			if (ctype_upper($caractere))
			    $nome_tabela .= "_" . strtolower($caractere);
			else
			    $nome_tabela .= $caractere;
		}

		return $nome_tabela;
	}
	
	public function salva() {
		if ($this->id)
			return $this->altera();
		else
			return $this->insere();
	}

	public function insere() {
		$this->atualiza_atributos();
			
		$campos = "";
		$valores = "";

		foreach ($this->campos as $key => $value)
			if ($key <> "id" and $key <> "erro_sql") {
				if ($value) {
					$campos .= $key . ", ";
					$valores .= (strtolower($value) == "null" ? "null, " : "'" . utf8_decode(addslashes($value)) . "', ");
				}
			}

		$campos = substr($campos, 0, strlen($campos) - 2);
		$valores = substr($valores, 0, strlen($valores) - 2);

		$sql = "INSERT INTO " . $this->tabela . " ($campos) VALUES ($valores)";
		
		$this->exibe_sql($sql);

		$resultado = $this->banco->executar($sql);

		$this->erro_sql = $this->banco->erro_sql;

		if ($resultado)
		    $this->id = mysql_insert_id();

		$this->banco->desconectar();

		return $resultado;
	}

	public function altera() {
		$this->atualiza_atributos();

		$campos_alterar = "";

		foreach ($this->campos as $key => $value)
		    if ($key <> "id" and $key <> "erro_sql")
		        if (isset($value))
		            $campos_alterar .= $key . " = " . (strtolower($value) == "null" ? "null" : "'" . utf8_decode(addslashes($value)) . "'") . ", ";

		$campos_alterar = substr($campos_alterar, 0, strlen($campos_alterar) - 2);

		$sql = "UPDATE " . $this->tabela . " SET $campos_alterar WHERE id = " . $this->id;
		
		$this->exibe_sql($sql);

		$resultado = $this->banco->executar($sql);

		$this->erro_sql = $this->banco->erro_sql;

		$this->banco->desconectar();

		return $resultado;
	}

	public function deleta() {
		$sql = "DELETE FROM " . $this->tabela . " WHERE id = " . $this->id;
		
		$this->exibe_sql($sql);

		$resultado = $this->banco->executar($sql);

		$this->erro_sql = $this->banco->erro_sql;

		$this->banco->desconectar();

		return $resultado;
	}

	public function busca($filtro = null, $ordem = null) {
		$filtrar = "";
		if ($filtro)
		    $filtrar = " WHERE " . (is_numeric($filtro) ? "id = $filtro" : "$filtro");

		$ordem = " ORDER BY " . ($ordem ? $ordem : "id");

		$sql = "SELECT * FROM " . $this->tabela . $filtrar . $ordem;
		
		$a_registros = $this->resultado_consulta($sql);

		if (!$a_registros)
    		return false;

		if (count($a_registros) == 1 and is_numeric($filtro)) {
			foreach($a_registros as $indice => $objeto)
			    foreach($objeto as $atributo => $valor)
			        $this->$atributo = $valor;

			return $this;
		}

		return $a_registros;
	}

	public function resultado_consulta($sql) {
		$this->exibe_sql($sql);
		
		$recurso = $this->banco->executar($sql);
		
		$this->erro_sql = $this->banco->erro_sql;
		
		$this->banco->desconectar();
		
		if (!$recurso)
			return false;
		
		if (mysql_num_rows($recurso) > 0) {
			$a_objetos = array();
			
			while ($registro = mysql_fetch_object($recurso)) {
				$a_exibir = array();
					
				foreach ($registro as $chave => $valor)
					$a_exibir[$chave] = $valor;
					
				$a_objetos[] = (object)$a_exibir;
			}
		}
		
		return (mysql_num_rows($recurso) > 0) ? $a_objetos : false;
	}
}
?>
