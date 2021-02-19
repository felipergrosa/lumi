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
    if(@$_POST['representante']){
        $representante = $_POST['representante'];
    }
    else {
        $representante = 0;
    }
    $con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
    $sql = new SqlServer($con_sql_server);
    $busca = $sql->BuscaNaturezaOperacao($empresa, $representante);
    echo json_encode(utf8ize($busca));
}

if($_POST['action'] == 'verifica_acesso_municipio'){
    $municipio = $_POST['municipio'];
    $representante = $representante_id;
    $con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
    $sql = new SqlServer($con_sql_server);
    $busca = $sql->VerificaAcessoMunicipio($municipio, $representante);
    echo json_encode(utf8ize($busca));
}
