<?php
@session_start();

header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__.'/../../../../../dist/php/general.inc.php';
ini_set('display_errors', 'on');
$prefix = "cadastro_pedidos_edit_form_";
$CdRepresentante = $representante_id;
if(@$_POST['action'] == 'continuar'){
    $Valida = new Valida();
    $obrigatorios = null;
    $cpfcnpj = $_POST['cpfcnpj'];
    $empresa = $_POST['empresa'];
    // $cpfcnpj = trim(str_replace('.', '', str_replace('-', '', str_replace('/', '', $cpfcnpj))));
    // if(strlen($cpfcnpj) == 11){
    //     if(!$Valida->validaCPF($cpfcnpj)){
    //         $retorno['erro'] = 1;
    //         $retorno['mensagem'] = 'CPF Inválido';
    //         exit;
    //     }
    //
    // }
    // elseif(strlen($cpfcnpj) == 14){
    //     if(!$Valida->validaCNPJ($cpfcnpj)){
    //         $retorno['erro'] = 1;
    //         $retorno['mensagem'] = 'CNPJ Inválido';
    //     }
    //
    // }
    // else {
    //     $retorno['erro'] = 1;
    //     $retorno['mensagem'] = 'Formato CPF/CNPJ Inválido';
    // }
    //
    //
    // if(@$retorno['erro'] == 1){
    //     echo json_encode($retorno);
    //     exit;
    // }

    // Verifica se cliente tem acesso a esta empresa
    // $sql = "SELECT a.*, b.nome as cliente_nome, c.nome_fantasia as empresa_nome
    // FROM clientes_empresas a
    // LEFT JOIN clientes b ON a.cliente=b.id
    // LEFT JOIN empresas c ON a.empresa=c.id
    // WHERE b.cpfcnpj = :cpfcnpj && a.empresa = :empresa";
    // $sql = $con->prepare($sql);
    // $sql->bindParam('cpfcnpj', $cpfcnpj);
    // $sql->bindParam('empresa', $empresa);
    $cpfcnpjlike = '%'.$_POST['cpfcnpj'].'%';
    $sql = "SELECT TOP 100 a.* FROM BusinessCadCliente a
    LEFT JOIN BusinessCadClienteLC b ON a.Cnpj_Cnpf=b.Cnpj_Cnpf
    WHERE (a.Cnpj_Cnpf = :cpfcnpj OR a.FsCliente LIKE :cpfcnpjlike OR a.RzCliente LIKE :cpfcnpjlike) AND b.CdEmpresa = :empresa AND a.CdRepresentante = :representante
    ";
    $sql = $con_sql_server->prepare($sql);
    $sql->bindParam('cpfcnpj', $_POST['cpfcnpj']);
    $sql->bindParam('cpfcnpjlike', $cpfcnpjlike);
    $sql->bindParam('empresa', $_POST['empresa']);
    $sql->bindParam('representante', $CdRepresentante);


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

        if($dd['name'] == "vendas_nova_venda_produto_id" && $dd["value"] != ""){
            for($i=0; $i<5; $i++){
                if($dados[$cont+$i]['value'] == ""){
                    $dados[$cont+$i]['value'] = null;
                }
            }

            $sql = "SELECT * FROM BusinessCadProduto WHERE CdProduto = :CdProduto";
            $sql = $con_sql_server->prepare($sql);
            $sql->bindParam('CdProduto', $dados[$cont]['value']);
            $sql->execute();
            $prod_sql = $sql->fetch(PDO::FETCH_ASSOC);

            $prod[$cont_pd]['id'] = $dados[$cont]['value'];
            $prod[$cont_pd]['nome'] = $dados[$cont+1]['value'];
            $prod[$cont_pd]['valor'] = $dados[$cont+2]['value'];
            $prod[$cont_pd]['qt'] = $dados[$cont+3]['value'];
            $prod[$cont_pd]['subtotal'] = $dados[$cont+4]['value'];
            $prod[$cont_pd]['sql'] = $prod_sql;
            $total_produtos = $total_produtos+$prod[$cont_pd]['subtotal'];

            $cont_pd++;
        }
        $info[$dd['name']] = $dd['value'];
        $cont++;

    }
    if($info['cadastro_pedidos_edit_form_data_pedido'] == NULL or $info['cadastro_pedidos_edit_form_data_pedido'] == ""){
        $info['cadastro_pedidos_edit_form_data_pedido'] = date("Y-m-d");
    }
        //produto
    $FlEspecificacao = null;
    $FlFalha = null;
    $Gramatura = null;
    $Observacao = null;
    $Negociado = null;
    //pedido
    $XML = null;
    $FlStatu = "";
    $FlEnvRecEmpresa = "S";
    $FlEnvRecRepre = "S";
    $DtCancelamento = null;
    $MotivoCancelamento = null;
    $Observacao = $info["cadastro_pedidos_edit_form_observacao"];
    $PeDesconto = $info["cadastro_pedidos_edit_form_desconto_padrao"];
    $PeDesconto2 = $info["cadastro_pedidos_edit_form_desconto_adic1"];
    $PeDesconto3 = $info["cadastro_pedidos_edit_form_desconto_adic2"];
    $PeDesconto4 = $info["cadastro_pedidos_edit_form_desconto_adic3"];
    $PeDesconto5 = $info["cadastro_pedidos_edit_form_desconto_part_com"];
    $FlFrete = "F";
    $DtEntrega = null;
    $RefCliente = null;
    if($info["cadastro_pedidos_edit_form_num_pedido_compra"] == ""){
        $info["cadastro_pedidos_edit_form_num_pedido_compra"] = "XXXXXX";
    }
    $CdPedidoEmpre = $info["cadastro_pedidos_edit_form_num_pedido_compra"];


    if($info['cadastro_pedidos_edit_form_num_pedido_representante'] == ""){
        $info['cadastro_pedidos_edit_form_num_pedido_representante'] = "XXXXXX";
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
    $info[$prefix.'cpfcnpj_old'] = $info[$prefix.'cpfcnpj'];
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

    $info[$prefix.'cpfcnpj'] = $info[$prefix.'cpfcnpj_old'];

    $info['cadastro_pedidos_edit_form_prioridade'] = (int) $info['cadastro_pedidos_edit_form_prioridade'];
    $info['cadastro_pedidos_edit_form_prioridade'] = 1;

    $sql_verifica_duplicate = "SELECT * FROM BusinessMovPedidoVenda WHERE
    CdRepresentante = :CdRepresentante AND
    CdEmpresa = :CdEmpresa AND
    CdPedidoRepre = :CdPedidoRepre AND
    CdPedidoEmpre = :CdPedidoEmpre";

    $sql_verifica_duplicate = $con_sql_server->prepare($sql_verifica_duplicate);
    $sql_verifica_duplicate->bindParam('CdRepresentante', $CdRepresentante);
    $sql_verifica_duplicate->bindParam('CdEmpresa', $info['cadastro_pedidos_edit_form_empresa']);
    $sql_verifica_duplicate->bindParam('CdPedidoRepre', $info['cadastro_pedidos_edit_form_num_pedido_representante']);
    $sql_verifica_duplicate->bindParam('CdPedidoEmpre', $CdPedidoEmpre);
    $sql_verifica_duplicate->execute();



    //
    // var_dump($CdRepresentante);
    // var_dump($info['cadastro_pedidos_edit_form_empresa']);
    // var_dump($info['cadastro_pedidos_edit_form_num_pedido_representante']);
    // var_dump($CdPedidoEmpre);
    //
    //
    // exit;
    if($sql_verifica_duplicate->rowCount() != 0){
        echo "Combinação de Num. Ped. Repres. e Ped. Comp. ja existem na base e devem ser únicos.";
        exit;
    }
    if(@$info["cadastro_pedidos_edit_form_cond_pagto"] == ""){
        echo "É necessário informar uma forma de pagamento VALIDA.";
        exit;
    }

    try {
        $sql = "INSERT INTO BusinessMovPedidoVenda
        (
            CdRepresentante,
            CdEmpresa,
            CdPedidoRepre,
            CdPedidoEmpre,
            Cnpj_Cnpf,
            DtPedido,
            PRIORIDADE,
            CdNatureza,
            XML,
            FlStatus,
            FlEnvRecEmpresa,
            FlEnvRecRepre,
            DtCancelamento,
            MotivoCancelamento,
            Observacao,
            PeDesconto,
            PeDesconto2,
            PeDesconto3,
            PeDesconto4,
            PeDesconto5,
            FlFrete,
            DtEntrega,
            CdTabela,
            CdCondPgto,
            CdTransportadora,
            RefRepresentante,
            RefCliente
        )
        VALUES (
            :CdRepresentante,
            :CdEmpresa,
            :CdPedidoRepre,
            :CdPedidoEmpre,
            :Cnpj_Cnpf,
            :DtPedido,
            :PRIORIDADE,
            :CdNatureza,
            :XML,
            :FlStatus,
            :FlEnvRecEmpresa,
            :FlEnvRecRepre,
            :DtCancelamento,
            :MotivoCancelamento,
            :Observacao,
            :PeDesconto,
            :PeDesconto2,
            :PeDesconto3,
            :PeDesconto4,
            :PeDesconto5,
            :FlFrete,
            :DtEntrega,
            :CdTabela,
            :CdCondPgto,
            :CdTransportadora,
            :RefRepresentante,
            :RefCliente
        )";
        $sql = $con_sql_server->prepare($sql);
        $sql->bindParam('CdRepresentante', $CdRepresentante);
        $sql->bindParam('CdEmpresa', $info['cadastro_pedidos_edit_form_empresa']);
        $sql->bindParam('CdPedidoRepre', $info['cadastro_pedidos_edit_form_num_pedido_representante']);
        $sql->bindParam('CdPedidoEmpre', $CdPedidoEmpre);
        $sql->bindParam('Cnpj_Cnpf', $info['cadastro_pedidos_edit_form_cpfcnpj']);
        $sql->bindParam('DtPedido', $info['cadastro_pedidos_edit_form_data_pedido']);
        $sql->bindParam('PRIORIDADE', $info['cadastro_pedidos_edit_form_prioridade']);
        $sql->bindParam('CdNatureza', $info['cadastro_pedidos_edit_form_natureza_operacao']);
        $sql->bindParam('XML', $XML);
        $sql->bindParam('FlStatus', $FlStatus);
        $sql->bindParam('FlEnvRecEmpresa', $FlEnvRecEmpresa);
        $sql->bindParam('FlEnvRecRepre', $FlEnvRecRepre);
        $sql->bindParam('DtCancelamento', $DtCancelamento);
        $sql->bindParam('MotivoCancelamento', $MotivoCancelamento);
        $sql->bindParam('Observacao', $Observacao);
        $sql->bindParam('PeDesconto', $PeDesconto);
        $sql->bindParam('PeDesconto2', $PeDesconto2);
        $sql->bindParam('PeDesconto3', $PeDesconto3);
        $sql->bindParam('PeDesconto4', $PeDesconto4);
        $sql->bindParam('PeDesconto5', $PeDesconto5);
        $sql->bindParam('FlFrete', $FlFrete);
        $sql->bindParam('DtEntrega', $DtEntrega);
        $sql->bindParam('CdTabela', $info['cadastro_pedidos_edit_form_tabela_preco']);
        $sql->bindParam('CdCondPgto', $info['cadastro_pedidos_edit_form_cond_pagto']);
        $sql->bindParam('CdTransportadora', $info['cadastro_pedidos_edit_form_transportadora']);
        $sql->bindParam('RefRepresentante', $info['cadastro_pedidos_edit_form_num_pedido_representante']);
        $sql->bindParam('RefCliente', $RefCliente);
        $executar = $sql->execute();
        if(!$executar){
            echo "Erro na inserção do pedido (SQL)";
            exit;
        }
    } catch(Exception $e){
        echo $e->getMessage();
        exit;
    }
    catch(PDOException $pdo){
        echo $pdo->getMessage();
    	exit;
    }


    foreach($prod as $pd){
        try {
            $sql = "INSERT INTO BusinessMovPedidoVendaItem
            (
                CdRepresentante,
                CdEmpresa,
                CdPedidoRepre,
                CdPedidoEmpre,
                CdProduto,
                RefCliente,
                FlEspecificacao,
                FlFalha,
                Gramatura,
                DsProCompl,
                Observacao,
                PeDesconto4,
                PeDesconto5,
                Negociado,
                PeIpi,
                PeIcms,
                PeReducao,
                QtProduto,
                QtBaixado,
                Unitario,
                PeDesconto,
                PeDesconto2,
                PeDesconto3
            )
            VALUES (
                :CdRepresentante,
                :CdEmpresa,
                :CdPedidoRepre,
                :CdPedidoEmpre,
                :CdProduto,
                :RefCliente,
                :FlEspecificacao,
                :FlFalha,
                :Gramatura,
                :DsProCompl,
                :Observacao,
                :PeDesconto4,
                :PeDesconto5,
                :Negociado,
                :PeIpi,
                :PeIcms,
                :PeReducao,
                :QtProduto,
                :QtBaixado,
                :Unitario,
                :PeDesconto,
                :PeDesconto2,
                :PeDesconto3
            )";

            $sql = $con_sql_server->prepare($sql);
            $sql->bindParam('CdRepresentante', $CdRepresentante);
            $sql->bindParam('CdEmpresa', $info['cadastro_pedidos_edit_form_empresa']);
            $sql->bindParam('CdPedidoRepre', $info['cadastro_pedidos_edit_form_num_pedido_representante']);
            $sql->bindParam('CdPedidoEmpre', $CdPedidoEmpre);
            $sql->bindParam('CdProduto', $pd['id']);
            $sql->bindParam('RefCliente', $RefCliente);
            $sql->bindParam('FlEspecificacao', $FlEspecificacao);
            $sql->bindParam('FlFalha', $FlFalha);
            $sql->bindParam('Gramatura', $Gramatura);
            $sql->bindParam('DsProCompl', $pd['nome']);
            $sql->bindParam('Observacao', $Observacao);
            $sql->bindParam('PeDesconto4', $PeDesconto4);
            $sql->bindParam('PeDesconto5', $PeDesconto5);
            $sql->bindParam('Negociado', $Negociado);
            $sql->bindParam('PeIpi', $pd['sql']['PeIpi']);
            $sql->bindParam('PeIcms', $pd['sql']['PeIcms']);
            $sql->bindParam('PeReducao', $pd['sql']['PeReducao']);
            $sql->bindParam('QtProduto', $pd['qt']);
            $sql->bindParam('QtBaixado', $pd['qt']);
            $sql->bindParam('Unitario', $pd['valor']);
            $sql->bindParam('PeDesconto', $PeDesconto);
            $sql->bindParam('PeDesconto2', $PeDesconto2);
            $sql->bindParam('PeDesconto3', $PeDesconto3);
            $executar = $sql->execute();
            if(!$executar){
                echo "Erro na inserção do item do pedido (SQL)";
                exit;
            }
        } catch(Exception $e){
            echo $e->getMessage();
            exit;
        }
        catch(PDOException $pdo){
            echo $pdo->getMessage();
            exit;
        }
    }

    //Remover exit para concluir

    echo 0;
    exit;

}
exit;
