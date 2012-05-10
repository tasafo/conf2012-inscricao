<?php
require_once dirname(__FILE__) . '/../util/constantes.php';

class Banco {
	private $usuario;
	private $senha;
	private $banco_de_dados;
	private $banco_de_dados_selecionado;
	private $conexao;
	public $erro_sql;

	function __construct() {
		$this->usuario = BANCO_USUARIO;
		$this->senha = BANCO_SENHA;
		$this->banco_de_dados = BANCO_BASE_DADOS;
	}

	function conectar() {
		if (!isset($this->conexao)) {
			$this->conexao = mysql_connect(BANCO_SERVIDOR, $this->usuario, $this->senha);
				
			if (!$this->conexao)
			    die("Nao foi possivel conectar: " . mysql_error());
		}

		$this->seleciona_banco_de_dados();
	}

	function desconectar() {
		if (isset($this->conexao))
		    mysql_close($this->conexao);
	}

	function seleciona_banco_de_dados() {
		$this->banco_de_dados_selecionado = mysql_select_db($this->banco_de_dados, $this->conexao);

		if (!$this->banco_de_dados_selecionado)
		    die("Nao foi possivel utilizar o banco: " . $this->banco_de_dados . " - " . mysql_error());
	}

	function executar($query) {
		$this->conexao = $this->conectar();

		$resultado = mysql_query($query);

		$this->erro_sql = "";
		if (!$resultado)
		    $this->erro_sql = "Erro SQL = ". mysql_error();

		return $resultado;
	}

	function begin() {
		$this->executar("BEGIN");
	}

	function commit() {
		$this->executar("COMMIT");
	}

	function rollback() {
		$this->executar("ROLLBACK");
	}
}
?>
