<?php
session_start();

$arquivo_atual = array_pop(explode('/', $_SERVER['PHP_SELF']));

$perfil_usuario_logado = $_SESSION['logado']['perfil'];

$a_menu = array(
    array('sql', '', 'admin'),
    array('addEmployee', '', 'admin'),
    array('relatorioIndividual', 'Inscrições Individuais', 'admin'),
    array('relatorioEmpresas', 'Inscrições por Instituições', 'admin'),
    array('relatorioInscritos', 'Listagem dos Inscritos Pagantes', 'admin, organizador'),
    array('relatorioFaturamentoIndividual', 'Faturamento de Inscrições Individuais', 'admin'),
    array('relatorioPresentes', 'Relatório de Presentes', 'admin, organizador'),
    array('relatoriosTela', 'Outros Relatórios', 'admin'),
    array('pagamentosPorCompensacao', 'Pagamentos por data de compensação', 'admin'),
    array('manutencaoInscritos', 'Manutenção dos Inscritos', 'admin'),
    array('importarPagamentos', 'Importar Pagamentos do Pagseguro', 'admin'),
    array('envioEmailAviso', 'Enviar avisos por e-mail', 'admin'),
    array('envioCertificadoUsuarios', 'Organizadores e Palestrantes', 'admin'),
    array('registrarPresencaInscrito', 'Registrar Presença do Inscrito', 'admin, organizador'),
    array('saques', 'Saques', 'admin, organizador')
);

if (!$_SESSION['logado']) {
?>
    <script>
        alert('Acesso nao permitido! Faca o login no sistema.');
        window.location='index.php';
    </script>
<?php
} else {
    foreach ($a_menu as $menu) {
        $arquivo = $menu[0];
        $opcao = $menu[1];
        $perfis = $menu[2];
        
        if ( "$arquivo.php" == $arquivo_atual && !strstr($perfis, $perfil_usuario_logado) ) {
            die("<script>alert('Acesso nao permitido!'); window.location='menu.php'</script>");
        }
    }
?>
<table width="100%">
    <tr>
        <td width="33%"><?php echo "<b style='color: blue'>Usuário:</b> " . $_SESSION['logado']['nome'] . " - " . $_SESSION['logado']['perfil'] ?></td>
        <td width="33%" align="center"><a href="menu.php">Menu Principal</a></td>
        <td width="33%" align="right"><a href="index.php">Sair</a></td>
    </tr>
</table>
<hr>
<?php
}
?>