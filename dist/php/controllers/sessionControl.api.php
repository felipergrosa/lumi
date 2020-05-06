<?php
header('Access-Control-Allow-Origin: *');
session_start();
// var_dump($_SESSION);
require __DIR__.'/../general.inc.php';
function checkSession(){
    if($_SESSION['nobre_usuario_login'] && $_SESSION['nobre_usuario_nome']){
        return true;
    }
    else {
        return false;
    }
}
function getUserData(){
    $dados_usuario['usuario'] = $_SESSION['usuario'];
    $dados_usuario['nome'] = $_SESSION['nome'];
    $dados_usuario['userid'] = $_SESSION['userid'];
    $dados_usuario['isroot'] = $_SESSION['isroot'];
    return $dados_usuario;
}



if($_POST['action'] == 'checkSession'){
    echo(json_encode(checkSession()));
    exit;
}
if($_POST['action'] == 'unsetSession'){
    unset($_SESSION['nobre_usuario_id']);
    unset($_SESSION['nobre_usuario_login']);
    unset($_SESSION['nobre_usuario_nome']);
    echo true;
    exit;
}
if($_POST['action'] == 'getUserData'){
    echo json_encode(getUserData(), JSON_UNESCAPED_UNICODE);
    exit;
}


if($_POST['action'] == 'exibeMenuEsquerda'){
	$globalvar_username = $_SESSION['nome'];
	$globalvar_userid = $_SESSION['userid'];
	//var_dump( $_SESSION['isroot']);
    $ControleAcessos = new ControleAcessos($con2, $con, $globalvar_userid);
    echo '<li class="active treeview"><a onclick="javascript: CarregaPagina(\'index_conteudo.html\');">
        <i class="fa fa-dashboard" ></i> <span>Dashboard</span>
    </a>
</li>';
	$ControleAcessos->ExibeMenu();
}
