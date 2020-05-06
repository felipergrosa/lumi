<?php
header('Access-Control-Allow-Origin: *');
session_start();
require __DIR__.'/../general.inc.php';

if(@$_POST['action'] == 'GetEstados'){
    $sql = "SELECT * FROM dados_estados ORDER BY sigla ASC";
    $sql = $con->query($sql);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($row);
    exit;
}
