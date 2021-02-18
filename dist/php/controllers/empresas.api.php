<?php
header('Access-Control-Allow-Origin: *');
@session_start();
require __DIR__.'/../general.inc.php';
ini_set('display_errors', 'on');
$con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
if(@$_POST['action'] == 'GetEmpresas'){
    // $id = $_POST['id'];
    // $Cnpj_Cnpf = $_POST['Cnpj_Cnpf'];
    // $CdRepresentante = $_POST['CdRepresentante'];
        $sql = "SELECT * FROM BusinessCadPermiCondPgto WHERE CdRepresentante = :CdRepresentante";
        $sql = $con_sql_server->prepare($sql);
        $sql->bindParam('CdRepresentante', $representante_id);
        $sql->execute();
        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
            $empresa_ativa[$row['CdEmpresa']] = true;
        }

    $sql = new SqlServer($con_sql_server);
    $row_res_sql = $sql->BuscaEmpresas();
    $cont = 0;
    foreach($row_res_sql as $row_res){
        if(@$empresa_ativa[$row_res['id']]){
            $row[$cont] = $row_res;
            @$row[$cont]['ativo'] = $empresa_ativa[$row_res['id']];
        }
        $cont++;
    }

    echo json_encode(utf8ize($row));
    exit;
}
