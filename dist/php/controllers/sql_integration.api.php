<?php
header('Access-Control-Allow-Origin: *');
session_start();
require __DIR__.'/../general.inc.php';

if($_POST['action'] == "get_tabelas_precos"){
    $empresa = $_POST['empresa'];
    $con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
    $sql = new SqlServer($con_sql_server);
    $busca = $sql->BuscaTabelas($empresa);
    echo json_encode(utf8ize($busca));
}
if($_POST['action'] == "get_cond_pagto"){
    $empresa = $_POST['empresa'];
    $con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
    $sql = new SqlServer($con_sql_server);
    $busca = $sql->BuscaCondicoesPagamento($empresa);
    echo json_encode(utf8ize($busca));
}
if($_POST['action'] == "get_transportadoras"){
    $empresa = $_POST['empresa'];
    $con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
    $sql = new SqlServer($con_sql_server);
    $busca = $sql->BuscaTransportadoras($empresa);
    echo json_encode(utf8ize($busca));
}
if($_POST['action'] == "get_natureza_operacao"){
    $empresa = $_POST['empresa'];
    $con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
    $sql = new SqlServer($con_sql_server);
    $busca = $sql->BuscaNaturezaOperacao($empresa);
    echo json_encode(utf8ize($busca));
}