<?php
require_once __DIR__.'/../general.inc.php';
$user = $_POST['user'];
$pass = $_POST['pass'];
$pass = sha1(md5(sha1(md5($pass))));


$sql = "SELECT * FROM usuarios WHERE login = :user && pass = :pass";
$sql = $con->prepare($sql);
$sql->bindParam('user', $user);
$sql->bindParam('pass', $pass);
$sql->execute();
if($sql->rowCount() > 0){
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    @session_start();
    $_SESSION['nobre_usuario_id'] = $row['id'];
    $_SESSION['nobre_usuario_login'] = $row['login'];
    $_SESSION['nobre_usuario_nome'] = $row['nome'];
    $_SESSION['nobre_usuario_titulo'] = 'Testador do sistema';
    $_SESSION['nobre_usuario_descricao'] = 'Testador do sistema descrição';
    $_SESSION['nobre_usuario_foto'] = 'null.png';
    if($row['general_level'] == 'ROOT'){
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
