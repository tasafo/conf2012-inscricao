<?php
require_once dirname(__FILE__) . '/../general/autoload.php';

class EmpresaDAO extends AbstractDAO {
	public $id;
	public $nome;
	public $responsavel;
	public $email;
	public $cep;

	function __construct() {
		parent::__construct($this);
	}
}
?>