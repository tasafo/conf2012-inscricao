<?php
require_once dirname(__FILE__) . '/../general/autoload.php';

class TipoInscricaoDAO extends AbstractDAO {
	public $id;
	public $descricao;
	public $valor;
	public $status;

	function __construct() {
		parent::__construct($this);
	}
}
?>