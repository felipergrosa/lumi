<?php
header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__.'/../../../../../dist/php/general.inc.php';
ini_set('display_errors', 'on');
@session_start();
$prefix = "cadastro_pedidos_edit_form_";
if(@$_POST['action'] == 'continuar'){
    $Valida = new Valida();
    $obrigatorios = null;
    $cpfcnpj = $_POST['cpfcnpj'];
    $empresa = $_POST['empresa'];
    $cpfcnpj = trim(str_replace('.', '', str_replace('-', '', str_replace('/', '', $cpfcnpj))));
    if(strlen($cpfcnpj) == 11){
        if(!$Valida->validaCPF($cpfcnpj)){
            $retorno['erro'] = 1;
            $retorno['mensagem'] = 'CPF Inválido';
            exit;
        }

    }
    elseif(strlen($cpfcnpj) == 14){
        if(!$Valida->validaCNPJ($cpfcnpj)){
            $retorno['erro'] = 1;
            $retorno['mensagem'] = 'CNPJ Inválido';
        }

    }
    else {
        $retorno['erro'] = 1;
        $retorno['mensagem'] = 'Formato CPF/CNPJ Inválido';
    }


    if(@$retorno['erro'] == 1){
        echo json_encode($retorno);
        exit;
    }

    // Verifica se cliente tem acesso a esta empresa
    // $sql = "SELECT a.*, b.nome as cliente_nome, c.nome_fantasia as empresa_nome
    // FROM clientes_empresas a
    // LEFT JOIN clientes b ON a.cliente=b.id
    // LEFT JOIN empresas c ON a.empresa=c.id
    // WHERE b.cpfcnpj = :cpfcnpj && a.empresa = :empresa";
    // $sql = $con->prepare($sql);
    // $sql->bindParam('cpfcnpj', $cpfcnpj);
    // $sql->bindParam('empresa', $empresa);
    $sql = "SELECT TOP 100 * FROM BusinessCadCliente
    WHERE Cnpj_Cnpf = :cpfcnpj";
    $sql = $con_sql_server->prepare($sql);
    $sql->bindParam('cpfcnpj', $_POST['cpfcnpj']);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);

    if(!$row){
        $retorno['erro'] = 2;
        $retorno['mensagem'] = 'Cliente não encontrado';
        echo json_encode($retorno);
        exit;

        $sql = "SELECT * FROM clientes WHERE cpfcnpj = :cpfcnpj";
        $sql = $con->prepare($sql);
        $sql->bindParam('cpfcnpj', $cpfcnpj);
        $sql->execute();
        if($sql->rowCount() == 0){
            $retorno['erro'] = 2;
            $retorno['mensagem'] = 'Cliente não encontrado';
        }
        else {
            $retorno['erro'] = 1;
            $retorno['mensagem'] = 'Cliente nao autorizado para esta empresa';
        }
    }
    else {
        $retorno['erro'] = 0;
        $retorno['dados'] = $row;
    }


    echo json_encode($retorno);
    exit;
}


if($_POST['action'] == 'novo'){
    $Valida = new Valida();
    $obrigatorios = Array('cpfcnpj', 'empresa');

    $dados = $_POST['dados'];
    $cont = 0;
    $cont_pd = 0;
    $total_produtos = 0;
    foreach ($dados as $dd) {
        if($dd['value'] == ""){
            $dd['value'] = null;
        }

        if($dd['name'] == "vendas_nova_venda_produto_id"){
            for($i=0; $i<5; $i++){
                if($dados[$cont+$i]['value'] == ""){
                    $dados[$cont+$i]['value'] = null;
                }
            }

            $prod[$cont_pd]['id'] = $dados[$cont]['value'];
            $prod[$cont_pd]['nome'] = $dados[$cont+1]['value'];
            $prod[$cont_pd]['valor'] = $dados[$cont+2]['value'];
            $prod[$cont_pd]['qt'] = $dados[$cont+3]['value'];
            $prod[$cont_pd]['subtotal'] = $dados[$cont+4]['value'];
            $total_produtos = $total_produtos+$prod[$cont_pd]['subtotal'];

            $cont_pd++;
        }
        $info[$dd['name']] = $dd['value'];
        $cont++;

    }



    $error_message = "";
    $error = 0;
    foreach($obrigatorios as $ob){
        if(!isset($info[$prefix.$ob]) or $info[$prefix.$ob] == ""){
            $error++;
            $error_message .= "$ob é requerido <br />";
        }
    }

    if($error > 0){
        echo $error_message;
        exit;
    }

    $info[$prefix.'cpfcnpj'] = trim(str_replace('.', '', str_replace('-', '', str_replace('/', '', $info[$prefix.'cpfcnpj']))));
    $cpfcnpj = $info[$prefix.'cpfcnpj'];
    if(strlen($cpfcnpj) == 11){
        if(!$Valida->validaCPF($cpfcnpj)){
            echo 'CPF Inválido';
            exit;
        }

    }
    elseif(strlen($cpfcnpj) == 14){
        if(!$Valida->validaCNPJ($cpfcnpj)){
            echo 'CNPJ Inválido';
            exit;
        }

    }
    else {
        echo 'Formato CPF/CNPJ Inválido';
        exit;
    }

    $sql_cliente = "SELECT id FROM clientes WHERE cpfcnpj = :cpfcnpj";
    $sql_cliente = $con->prepare($sql_cliente);
    $sql_cliente->bindParam('cpfcnpj', $cpfcnpj);
    $sql_cliente->execute();

    $row_cliente = $sql_cliente->fetch(PDO::FETCH_ASSOC);
    $info[$prefix.'cliente'] = $row_cliente['id'];

    $sql = "INSERT INTO pedidos
        (
            representante,
            empresa,
            cliente,
            prioridade,
            frete,
            num_pedido_representante,
            data_pedido,
            data_previsao_faturamento,
            num_pedido_compra,
            tabela_preco,
            cond_pagto,
            transportadora,
            desconto_padrao,
            desconto_adic1,
            desconto_adic2,
            desconto_adic3,
            desconto_part_com,
            natureza_operacao,
            total_produtos,
            observacao,
            data_cadastro,
            usuario_cadastro
        )

        VALUES (
            1,
            :empresa,
            :cliente,
            :prioridade,
            :frete,
            :num_pedido_representante,
            :data_pedido,
            :data_previsao_faturamento,
            :num_pedido_compra,
            :tabela_preco,
            :cond_pagto,
            :transportadora,
            :desconto_padrao,
            :desconto_adic1,
            :desconto_adic2,
            :desconto_adic3,
            :desconto_part_com,
            :natureza_operacao,
            :total_produtos,
            :observacao,
            NOW(),
            1
            )";
    try {
    $sql = $con->prepare($sql);

    $sql->bindParam('empresa', $info[$prefix.'empresa']);
    $sql->bindParam('cliente', $info[$prefix.'cliente']);
    $sql->bindParam('prioridade', $info[$prefix.'prioridade']);
    $sql->bindParam('frete', $info[$prefix.'frete']);
    $sql->bindParam('num_pedido_representante', $info[$prefix.'num_pedido_representante']);
    $sql->bindParam('data_pedido', $info[$prefix.'data_pedido']);
    $sql->bindParam('data_previsao_faturamento', $info[$prefix.'data_previsao_faturamento']);
    $sql->bindParam('num_pedido_compra', $info[$prefix.'num_pedido_compra']);
    $sql->bindParam('tabela_preco', $info[$prefix.'tabela_preco']);
    $sql->bindParam('cond_pagto', $info[$prefix.'cond_pagto']);
    $sql->bindParam('transportadora', $info[$prefix.'transportadora']);
    $sql->bindParam('desconto_padrao', $info[$prefix.'desconto_padrao']);
    $sql->bindParam('desconto_adic1', $info[$prefix.'desconto_adic1']);
    $sql->bindParam('desconto_adic2', $info[$prefix.'desconto_adic2']);
    $sql->bindParam('desconto_adic3', $info[$prefix.'desconto_adic3']);
    $sql->bindParam('desconto_part_com', $info[$prefix.'desconto_part_com']);
    $sql->bindParam('natureza_operacao', $info[$prefix.'natureza_operacao']);
    $sql->bindParam('total_produtos', $total_produtos);
    $sql->bindParam('observacao', $info[$prefix.'observacao']);

    $sql->execute();

    }
    catch(PDOException $e){
        $error_message = $e->getMessage();
        echo $error_message;
        exit;
    }

    $id_pedido = $con->lastInsertId();

    foreach($prod as $pd){
        if($pd['id'] != ""){
            $sql = "INSERT INTO pedidos_produtos (pedido, produto_id, produto_nome, produto_valor, produto_qt) VALUES
            (:pedido, :produto_id, :produto_nome, :produto_valor, :produto_qt)";
            $sql = $con->prepare($sql);
            $sql->bindParam('pedido', $id_pedido);
            $sql->bindParam('produto_id', $pd['id']);
            $sql->bindParam('produto_nome', $pd['nome']);
            $sql->bindParam('produto_valor', $pd['valor']);
            $sql->bindParam('produto_qt', $pd['qt']);
            $sql->execute();
        }
    }

    echo 0;
    exit;

}
exit;
