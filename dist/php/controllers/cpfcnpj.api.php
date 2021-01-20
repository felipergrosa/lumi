<?php
header('Access-Control-Allow-Origin: *');
session_start();
require __DIR__.'/../general.inc.php';

if(@$_POST['action'] == 'valida'){
    $continuar = false;
    $Valida = new Valida();
    // var_dump($_POST['cpfcnpj']);
    $cpfcnpj = $_POST['cpfcnpj'];
    $cpfcnpj = trim(str_replace('.', '', str_replace('-', '', str_replace('/', '', $cpfcnpj))));
    // var_dump(strlen($cpfcnpj));
    if(strlen($cpfcnpj) == 11){
        if(!$Valida->validaCPF($cpfcnpj)){
            $resultado['erro'] = 1;
            $resultado['resposta'] = 'CPF Inválido';
        }
        else {
            $resultado['erro'] = 0;
            $resultado['resposta'] = 'CPF Validado';
            $continuar = true;
        }
    }
    elseif(strlen($cpfcnpj) == 14){
        if(!$Valida->validaCNPJ($cpfcnpj)){
            $resultado['erro'] = 1;
            $resultado['resposta'] = 'CNPJ Inválido';
        }
        else {
            $resultado['erro'] = 0;
            $resultado['resposta'] = 'CNPJ Validado';
            $continuar = true;

        }

    }
    else {
        $resultado['erro'] = 1;
        $resultado['resposta'] = 'Formato CPF/CNPJ Inválido';
    }

    if($continuar){
        //$con_sql_server
        $sql = "SELECT * FROM BusinessCadCliente WHERE Cnpj_Cnpf = :cpfcnpj";
        $sql = $con_sql_server->prepare($sql);
        $sql->bindParam('cpfcnpj', $_POST['cpfcnpj']);
        $sql->execute();
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        if($row){
            $resultado['existe'] = 1;
            $resultado['dados'] = $row;
        }
        else {
            $resultado['existe'] = 0;
        }
    }

    echo json_encode($resultado);
}
