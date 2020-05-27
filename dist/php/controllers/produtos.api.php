<?php
header('Access-Control-Allow-Origin: *');
session_start();
require __DIR__.'/../general.inc.php';
if($_POST['action'] == "busca_produtos"){
    $tabela = $_POST['tabela'];
    $busca = $_POST['busca'];
    $con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
    $sql = new SqlServer($con_sql_server);
    $busca = $sql->BuscaProduto($busca, $tabela, false, 5);
    if(@$busca[0]){
        $retorno['fetch'] = $busca;
        $retorno['status'] = 'ok';
        echo json_encode(utf8ize($retorno));
    }
    else {
        $retorno['status'] = 'erro';
        echo json_encode(utf8ize($retorno));
    }
    exit;
}
if($_POST['action'] == "busca_produtos_id"){
    $tabela = $_POST['tabela'];
    $busca = $_POST['busca'];
    $con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
    $sql = new SqlServer($con_sql_server);
    $busca = $sql->BuscaProduto($busca, $tabela, true, 5);
    if(@$busca[0]){
        $retorno['fetch'] = $busca;
        $retorno['status'] = 'ok';
        echo json_encode($retorno);
    }
    else {
        $retorno['status'] = 'erro';
        echo json_encode($retorno);
    }
    exit;
}
function utf8ize( $mixed ) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
    }
    return $mixed;
}