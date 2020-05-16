<?php
header('Access-Control-Allow-Origin: *');
session_start();
require __DIR__.'/../general.inc.php';

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
    $sql = "SELECT *, nome_fantasia as nome FROM empresas ORDER BY nome ASC";
    $sql = $con->query($sql);
    $cont = 0;
    while($row_res = $sql->fetch(PDO::FETCH_ASSOC)){
        $row[$cont] = $row_res;
        @$row[$cont]['ativo'] = $empresa_ativa[$row_res['id']];
        $cont++;
    }

    echo json_encode($row);
    exit;
}
