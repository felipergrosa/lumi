<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__.'/../../dist/php/general.inc.php';
@session_start();
$id = $_SESSION['sig4_usuario_id'];

$sql = "SELECT * FROM usuarios WHERE id = '$id'";
$sql = $con->query($sql);
$row = $sql->fetch(PDO::FETCH_ASSOC);
$_SESSION['sig4_usuario_login'] = $row['login'];
$_SESSION['sig4_usuario_nome'] = $row['nome'];
if($row['general_level'] == 'ROOT'){
    $_SESSION['sig4_usuario_isroot'] = true;
}
exit;
