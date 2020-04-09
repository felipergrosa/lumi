<?php
header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__.'/../../../../dist/php/general.inc.php';
@session_start();
if($_POST['action'] == 'PopularDadosUsuario'){
    $id = $_SESSION['sig4_usuario_id'];
    $sql = "SELECT * FROM usuarios WHERE id = '$id'";
    $sql = $con->query($sql);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    echo json_encode($row);
}
if($_POST['action'] == 'SalvarDadosUsuario'){
    $id = $_SESSION['sig4_usuario_id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $sql = "UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id";
    $sql = $con->prepare($sql);
    $sql->bindParam('nome', $nome);
    $sql->bindParam('email', $email);
    $sql->bindParam('id', $id);
    $sql->execute();
}
exit;
