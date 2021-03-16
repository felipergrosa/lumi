<?php
header('Access-Control-Allow-Origin: *');
@session_start();
require __DIR__.'/../general.inc.php';
ini_set('display_errors', 'on');
$con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
// var_dump($_POST);
if(@$_POST['action'] == 'GetEmpresas'){
    // $id = $_POST['id'];
    if(@$_POST['Cnpj_Cnpf']){
        if(@$_POST['CdRepresentante'] && @$_POST['CdRepresentante'] != "0"){
            $CdRepresentante = $_POST['CdRepresentante'];
        }
        else {
            $CdRepresentante = $representante_id;
        }
        $Cnpj_Cnpf = $_POST['Cnpj_Cnpf'];
        $sql = "SELECT * FROM BusinessCadClienteLC WHERE
        CdRepresentante = :CdRepresentante AND
        Cnpj_Cnpf = :Cnpj_Cnpf";
        $sql = $con_sql_server->prepare($sql);
        $sql->bindParam('CdRepresentante', $CdRepresentante);
        $sql->bindParam('Cnpj_Cnpf', $Cnpj_Cnpf);
        $sql->execute();
        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
            $empresa_ativa_cliente[$row['CdEmpresa']] = true;
        }
    }

    // $CdRepresentante = $_POST['CdRepresentante'];
        $sql = "SELECT * FROM BusinessCadPermiCondPgto WHERE CdRepresentante = :CdRepresentante";
        $sql = $con_sql_server->prepare($sql);
        $sql->bindParam('CdRepresentante', $representante_id);
        $sql->execute();
        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
            $empresa_ativa_rep[$row['CdEmpresa']] = true;
        }

    $sql = new SqlServer($con_sql_server);
    $row_res_sql = $sql->BuscaEmpresas();
    $cont = 0;
    foreach($row_res_sql as $row_res){
        if(@$empresa_ativa_rep[$row_res['id']]){
            $row[$cont] = $row_res;
            @$row[$cont]['ativo_rep'] = $empresa_ativa_rep[$row_res['id']];
        }
        if(@$empresa_ativa_cliente[$row_res['id']]){
            @$row[$cont]['ativo'] = $empresa_ativa_cliente[$row_res['id']];
        }
        $cont++;
    }

    echo json_encode(utf8ize($row));
    exit;
}
