<?php
require_once dirname(__FILE__) . '/../general/autoload.php';

class ReceitaDespesaDAO extends AbstractDAO {
    public $id;
    public $data_lancamento;
    public $data_operacao;
    public $tipo;
    public $valor;
    public $responsavel;
    public $descricao;
    public $quem_registrou;

    function __construct() {
        parent::__construct($this);
    }
}
?>