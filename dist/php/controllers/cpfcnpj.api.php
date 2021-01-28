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
        $sql = "SELECT * FROM BusinessCadCliente WHERE Cnpj_Cnpf = :cpfcnpj AND CdRepresentante = :CdRepresentante";
        $sql = $con_sql_server->prepare($sql);
        $sql->bindParam('cpfcnpj', $_POST['cpfcnpj']);
        $sql->bindParam('CdRepresentante', $representante_id);

        $sql->execute();
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        if($row){
            $resultado['existe'] = 1;
            $resultado['dados'] = $row;

            $sql_complementar = "SELECT
            c.FsEmpresa as empresa_nome,
                b.CdEmpresa as empresa

                 FROM BusinessCadClienteLC b
            LEFT JOIN BusinessCadEmpresa c ON b.CdEmpresa=c.CdEmpresa

            WHERE b.Cnpj_Cnpf = :cpfcnpj AND b.CdRepresentante = :CdRepresentante
            AND c.CdEmpresa is not null";
            $sql_complementar = $con_sql_server->prepare($sql_complementar);
            $sql_complementar->bindParam('cpfcnpj', $_POST['cpfcnpj']);
            $sql_complementar->bindParam('CdRepresentante', $representante_id);

            $sql_complementar->execute();
            $cont_empresas = 0;
            while($row_complemetar = $sql_complementar->fetch(PDO::FETCH_ASSOC)){
                $empresas[$cont_empresas]["empresa"] = $row_complemetar['empresa'];
                $empresas[$cont_empresas]["empresa_nome"] = $row_complemetar['empresa_nome'];
                $cont_empresas++;
            }
            $resultado['dados']['empresas'] = $empresas;


        }
        else {
            $resultado['existe'] = 0;
        }
    }

    echo json_encode($resultado);
}
