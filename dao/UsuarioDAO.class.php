<?php
require_once dirname(__FILE__) . '/../general/autoload.php';

class UsuarioDAO extends AbstractDAO {
  public $id;
  public $nome;
  public $email;
  public $senha;
  public $perfis;

  function __construct() {
    parent::__construct($this);
  }
}
?> 
