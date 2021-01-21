<?php
header('Access-Control-Allow-Origin: *');
session_start();
require __DIR__.'/../general.inc.php';
ini_set('display_errors', 'on');
if(@$_POST['action'] == 'GetAll'){
    // $id = $_POST['id'];
    $sql = "SELECT *,
    CdNatureza as id,
    DsNatureza as descricao
     FROM BusinessCadNatOperacao";
    $sql = $con_sql_server->prepare($sql);
    $sql->execute();
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(utf8ize($row));
    exit;
}
