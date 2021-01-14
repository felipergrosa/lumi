<?php
header('Access-Control-Allow-Origin: *');
session_start();
require __DIR__.'/../general.inc.php';
$con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
if(@$_POST['action'] == 'GetEstados'){
    $sql = "SELECT Estado FROM BusinessCadMunicipio WHERE Estado != '' GROUP BY Estado ORDER BY Estado ";
    $sql = $con_sql_server->query($sql);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(utf8ize($row));
    exit;
}
if(@$_POST['action'] == 'GetEstadoSigla'){
    $sigla = $_POST['sigla'];
    $sql = "SELECT Estado FROM BusinessCadMunicipio WHERE Estado != '' GROUP BY Estado ORDER BY Estado ";
    $sql = $con_sql_server->prepare($sql);
    // $sql->bindParam('sigla', $sigla);
    $sql->execute();
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(utf8ize($row));
    exit;
    // echo $row['id'];
    exit;
}
