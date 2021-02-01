<?php
require_once __DIR__.'/../general.inc.php';
$user = $_POST['user'];
$pass = $_POST['pass'];
// $pass = sha1(md5(sha1(md5($pass))));

function CodificaSenhaNobre($senha){
    $senha = mb_strtoupper(trim($senha));
    $saida = "";
    for($i=0; $i<strlen($senha); $i++){
        $saida .= chr(ord(substr($senha, $i, 1))+$i+18);
    }
    return trim($saida);
}
$pass = CodificaSenhaNobre($pass);
$user = (string) $user;
// $sql = "SELECT * FROM BusinessCadRepresentante WHERE Login = :user AND ahneS = :pass";
$sql = "SELECT * FROM BusinessCadRepresentante WHERE Login = :user AND ahneS = :pass";

$sql = $con_sql_server->prepare($sql);
$sql->bindParam('user', $user);
$sql->bindParam('pass', $pass);
$sql->execute();

$row = $sql->fetch(PDO::FETCH_ASSOC);
if(@$row){
    // $row = $sql->fetch(PDO::FETCH_ASSOC);
    @session_start();
    $_SESSION['nobre_usuario_id'] = $row['CdRepresentante'];
    $_SESSION['nobre_usuario_login'] = $row['Login'];
    $_SESSION['nobre_usuario_nome'] = $row['RzRepresentante'];
    $_SESSION['nobre_usuario_titulo'] = 'Testador do sistema';
    $_SESSION['nobre_usuario_descricao'] = 'Testador do sistema descrição';
    $_SESSION['nobre_usuario_foto'] = 'null.png';
    if(@$row['general_level'] == 'ROOT'){
        $_SESSION['nobre_usuario_isroot'] = true;
    }
    $resposta['tipo'] = 1;
    $resposta['texto'] = 'Login bem sucedido, redirecionando...';
}
else {
    $resposta['tipo'] = 0;
    $resposta['texto'] = 'Usuario ou senha invalidos';
}

echo json_encode($resposta);
