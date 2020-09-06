<?php
header('Access-Control-Allow-Origin: *');
session_start();
require __DIR__.'/../general.inc.php';

if(@$_POST['action'] == 'GetEstados'){
    $sql = "SELECT * FROM dados_estados ORDER BY sigla ASC";
    $sql = $con->query($sql);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(utf8ize($row));
    exit;
}
if(@$_POST['action'] == 'GetEstadoSigla'){
    $sigla = $_POST['sigla'];
    $sql = "SELECT id FROM dados_estados WHERE sigla = :sigla";
    $sql = $con->prepare($sql);
    $sql->bindParam('sigla', $sigla);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    echo $row['id'];
    exit;
}
