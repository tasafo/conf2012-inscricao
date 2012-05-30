<?php
require_once dirname(__FILE__) . '/../general/autoload.php';

class SaqueDAO extends AbstractDAO {
    public $id;
    public $data_lancamento;
    public $data_saque;
    public $valor;
    public $quem_registrou;

    function __construct() {
        parent::__construct($this);
    }
    
    function total_saques() {
        $sql = "SELECT COUNT(*) AS total, SUM(valor) AS valor FROM saque";

        return $this->resultado_consulta($sql);
    }
}
?>