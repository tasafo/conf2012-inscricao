<?php
require_once dirname(__FILE__) . '/../general/autoload.php';

class IndividualDAO extends AbstractDAO {
	public $id;
	public $id_inscricao;
	public $nome;
	public $email;
	public $profissao;
	public $instituicao;
	public $cep;
	public $situacao = "A";
	public $presente = "N";
	public $quem_registrou_presenca;
	public $permito_divulgacao = "N";

	function __construct() {
		parent::__construct($this);
	}
}
?>