<?php
header('Access-Control-Allow-Origin: *');
session_start();
require __DIR__.'/../general.inc.php';

if(@$_POST['action'] == 'GetEmpresas'){
    $sql = "SELECT * FROM empresas ORDER BY nome ASC";
    $sql = $con->query($sql);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($row);
    exit;
}
