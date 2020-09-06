<?php
header('Access-Control-Allow-Origin: *');
session_start();
require __DIR__.'/../general.inc.php';
ini_set('display_errors', 'on');
$con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
if(@$_POST['action'] == 'GetEmpresas'){
    $id = $_POST['id'];
    if($id != 0){
        $sql = "SELECT * FROM clientes_empresas WHERE cliente = :id";
        $sql = $con->prepare($sql);
        $sql->bindParam('id', $id);
        $sql->execute();
        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
            $empresa_ativa[$row['empresa']] = true;
        }

    }
    $sql = new SqlServer($con_sql_server);
    $row_res_sql = $sql->BuscaEmpresas();
    $cont = 0;
    foreach($row_res_sql as $row_res){
        $row[$cont] = $row_res;
        @$row[$cont]['ativo'] = $empresa_ativa[$row_res['id']];
        $cont++;
    }

    echo json_encode(utf8ize($row));
    exit;
}
