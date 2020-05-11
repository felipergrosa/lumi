<?php
header('Access-Control-Allow-Origin: *');
session_start();
require __DIR__.'/../general.inc.php';

if(@$_POST['action'] == 'valida'){
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
        }

    }
    else {
        $resultado['erro'] = 1;
        $resultado['resposta'] = 'Formato CPF/CNPJ Inválido';
    }

    echo json_encode($resultado);
}
