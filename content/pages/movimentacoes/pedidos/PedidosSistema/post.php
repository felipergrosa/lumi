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

    $sql = "SELECT a.*, b.nome as cliente_nome, c.nome_fantasia as empresa_nome
    FROM clientes_empresas a
    LEFT JOIN clientes b ON a.cliente=b.id
    LEFT JOIN empresas c ON a.empresa=c.id
    WHERE b.cpfcnpj = :cpfcnpj && a.empresa = :empresa";
    $sql = $con->prepare($sql);
    $sql->bindParam('cpfcnpj', $cpfcnpj);
    $sql->bindParam('empresa', $empresa);
    $sql->execute();

    if($sql->rowCount() == 0){
        $sql = "SELECT * FROM clientes WHERE cpfcnpj = :cpfcnpj";
        $sql = $con->prepare($sql);
        $sql->bindParam('cpfcnpj', $cpfcnpj);
        $sql->execute();
        if($sql->rowCount() == 0){
            $retorno['erro'] = 1;
            $retorno['mensagem'] = 'Cliente não encontrado';
        }
        else {
            $retorno['erro'] = 1;
            $retorno['mensagem'] = 'Cliente nao autorizado para esta empresa';
        }
    }
    else {
        $retorno['erro'] = 0;
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        $retorno['dados'] = $row;
    }


    echo json_encode($retorno);
    exit;
}


if($_POST['action'] == 'GetUserList'){
    $sql = "SELECT a.* ,
    b.endereco as endereco_endereco,
    b.cidade as endereco_cidade,
    c.nome as endereco_estado

    FROM clientes a
    LEFT JOIN dados_enderecos b ON a.endereco=b.id
    LEFT JOIN dados_estados c ON b.estado=c.id";
    $sql = $con->query($sql);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($row);
}
if($_POST['action'] == 'GetUserData'){
    $id = (int) $_POST['id'];
    $sql = "SELECT a.*,
    DATE_FORMAT(a.data_cadastro, '%d-%m-%Y') as data_cadastro,
    b.cep as endereco_cep,
    b.endereco as endereco_endereco,
    b.complemento as endereco_complemento,
    b.bairro as endereco_bairro,
    b.cidade as endereco_cidade,
    b.estado as endereco_estado,
    b.numero as endereco_numero,

    c.cep as endereco_cobranca_cep,
    c.endereco as endereco_cobranca_endereco,
    c.complemento as endereco_cobranca_complemento,
    c.bairro as endereco_cobranca_bairro,
    c.cidade as endereco_cobranca_cidade,
    c.estado as endereco_cobranca_estado,
    c.numero as endereco_cobranca_numero,

    d.cep as endereco_entrega_cep,
    d.endereco as endereco_entrega_endereco,
    d.complemento as endereco_entrega_complemento,
    d.bairro as endereco_entrega_bairro,
    d.cidade as endereco_entrega_cidade,
    d.estado as endereco_entrega_estado,
    d.numero as endereco_entrega_numero

    FROM clientes a
    LEFT JOIN dados_enderecos b ON a.endereco=b.id
    LEFT JOIN dados_enderecos c ON a.endereco_cobranca=c.id
    LEFT JOIN dados_enderecos d ON a.endereco_entrega=d.id
    WHERE a.id = :id";
    $sql = $con->prepare($sql);
    $sql->bindParam('id', $id);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $row['resposta'] = $row;
    echo json_encode($row);
}
if($_POST['action'] == 'SalvarDadosUsuario'){
    $id = $_SESSION['nobre_usuario_id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $sql = "UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id";
    $sql = $con->prepare($sql);
    $sql->bindParam('nome', $nome);
    $sql->bindParam('email', $email);
    $sql->bindParam('id', $id);
    $sql->execute();
}
if($_POST['action'] == 'novo'){
    $Valida = new Valida();
    $obrigatorios = Array('cpfcnpj', 'empresa');

    $dados = $_POST['dados'];
    foreach ($dados as $dd) {
        if($dd['value'] == ""){
            $dd['value'] = null;
        }
        $info[$dd['name']] = $dd['value'];

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
    $sql->bindParam('observacao', $info[$prefix.'observacao']);

    $sql->execute();

    }
    catch(PDOException $e){
        $error_message = $e->getMessage();
        echo $error_message;
        exit;
    }

    echo 0;
    exit;

}
if($_POST['action'] == 'editar'){

        $EnderecosFunc = new Enderecos($con);
        $Valida = new Valida();

        $obrigatorios = array(
            'nome',
            'cpfcnpj'
        );
        $info['cadastro_clientes_edit_form_restricao'] = 0;
        $dados = $_POST['dados'];
        foreach ($dados as $dd) {
            if($dd['name'] == "cadastro_clientes_edit_form_restricao"){
                $dd['value'] = 1;
            }
            if($dd['value'] == ""){
                $dd['value'] = null;
            }
            $info[$dd['name']] = $dd['value'];
            $prefix_loop = "cadastro_clientes_edit_form_endereco_entrega";
            if(substr($dd['name'], 0, strlen($prefix_loop)) == $prefix_loop){
                $endereco_entrega[str_replace($prefix_loop.'_', "", $dd['name'])] = $dd['value'];
            }
            $prefix_loop = "cadastro_clientes_edit_form_endereco_cobranca";
            if(substr($dd['name'], 0, strlen($prefix_loop)) == $prefix_loop){
                $endereco_cobranca[str_replace($prefix_loop.'_', "", $dd['name'])] = $dd['value'];
            }
            $prefix_loop = "cadastro_clientes_edit_form_endereco";
            if(substr($dd['name'], 0, strlen($prefix_loop)) == $prefix_loop){
                $endereco_principal[str_replace($prefix_loop.'_', "", $dd['name'])] = $dd['value'];
            }

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

        $info[$prefix.'tel1'] = trim(str_replace(' ', '', str_replace('(', '', str_replace('-', '', str_replace(')', '', $info[$prefix.'tel1'])))));
        $info[$prefix.'fax'] = trim(str_replace(' ', '', str_replace('(', '', str_replace('-', '', str_replace(')', '', $info[$prefix.'fax'])))));
        $info[$prefix.'contato_compras_fone'] = trim(str_replace(' ', '', str_replace('(', '', str_replace('-', '', str_replace(')', '', $info[$prefix.'contato_compras_fone'])))));
        $info[$prefix.'contato_cobranca_fone'] = trim(str_replace(' ', '', str_replace('(', '', str_replace('-', '', str_replace(')', '', $info[$prefix.'contato_cobranca_fone'])))));
        $info[$prefix.'endereco_cobranca_contato_telefone'] = trim(str_replace(' ', '', str_replace('(', '', str_replace('-', '', str_replace(')', '', $info[$prefix.'endereco_cobranca_contato_telefone'])))));
        $info[$prefix.'endereco_entrega_contato_telefone'] = trim(str_replace(' ', '', str_replace('(', '', str_replace('-', '', str_replace(')', '', $info[$prefix.'endereco_entrega_contato_telefone'])))));




        $info[$prefix.'cpfcnpj'] = trim(str_replace('.', '', str_replace('-', '', str_replace('/', '', $info[$prefix.'cpfcnpj']))));
        $info[$prefix.'endereco_cep'] = str_replace('-', '', $info[$prefix.'endereco_cep']);

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


    try {
        $sql = "SELECT * FROM clientes WHERE cpfcnpj = :cpfcnpj && id != :id";
        $sql = $con->prepare($sql);
        $sql->bindParam('cpfcnpj', $info[$prefix.'cpfcnpj']);
        $sql->bindParam('id', $info[$prefix.'id']);
        $sql->execute();
    }
    catch(Exception $e){
        $error_message = $e->getMessage();
        echo $error_message;
        exit;
    }
    if($sql->rowCount() > 0){
        echo 'CPF/CNPJ ja existe.';
        exit;
    }

    $endereco_principal = $EnderecosFunc->novoEndereco($endereco_principal);
    $endereco_entrega = $EnderecosFunc->novoEndereco($endereco_entrega);
    $endereco_cobranca = $EnderecosFunc->novoEndereco($endereco_cobranca);



    $sql = "UPDATE clientes SET
    endereco = :endereco,
endereco_cobranca = :endereco_cobranca,
endereco_entrega = :endereco_entrega,
nome = :nome,
cpfcnpj = :cpfcnpj,
rgie = :rgie,
suframa = :suframa,
suframa_validade = :suframa_validade,
desconto = :desconto,
natureza_operacao = :natureza_operacao,
email1 = :email1,
tel1 = :tel1,
fax = :fax,
site = :site,
contato_compras = :contato_compras,
contato_compras_fone = :contato_compras_fone,
contato_compras_email = :contato_compras_email,
contato_cobranca = :contato_cobranca,
contato_cobranca_fone = :contato_cobranca_fone,
contato_cobranca_email = :contato_cobranca_email,
contato_cobranca_funcao = :contato_cobranca_funcao,
grupo = :grupo,
segmento_mercado = :segmento_mercado,
regiao = :regiao,
endereco_cobranca_contato = :endereco_cobranca_contato,
endereco_cobranca_contato_telefone = :endereco_cobranca_contato_telefone,
endereco_cobranca_contato_email = :endereco_cobranca_contato_email,
endereco_entrega_cnpj = :endereco_entrega_cnpj,
endereco_entrega_ie = :endereco_entrega_ie,
endereco_entrega_contato = :endereco_entrega_contato,
endereco_entrega_contato_telefone = :endereco_entrega_contato_telefone,
endereco_entrega_contato_email = :endereco_entrega_contato_email,
observacoes = :observacoes,
restricao = :restricao,
restricao_texto = :restricao_texto,
atividade = :atividade
WHERE id = :id";
    try {
    $sql = $con->prepare($sql);
    $sql->bindParam('id', $info[$prefix.'id']);
    $sql->bindParam('endereco', $endereco_principal);
    $sql->bindParam('endereco_cobranca', $endereco_cobranca);
    $sql->bindParam('endereco_entrega', $endereco_entrega);
    $sql->bindParam('nome', $info[$prefix.'nome']);
    $sql->bindParam('cpfcnpj', $info[$prefix.'cpfcnpj']);
    $sql->bindParam('rgie', $info[$prefix.'rgie']);
    $sql->bindParam('suframa', $info[$prefix.'suframa']);
    $sql->bindParam('suframa_validade', $info[$prefix.'suframa_validade']);
    $sql->bindParam('desconto', $info[$prefix.'desconto']);
    $sql->bindParam('natureza_operacao', $info[$prefix.'natureza_operacao']);
    $sql->bindParam('email1', $info[$prefix.'email1']);
    $sql->bindParam('tel1', $info[$prefix.'tel1']);
    $sql->bindParam('fax', $info[$prefix.'fax']);
    $sql->bindParam('site', $info[$prefix.'site']);
    $sql->bindParam('contato_compras', $info[$prefix.'contato_compras']);
    $sql->bindParam('contato_compras_fone', $info[$prefix.'contato_compras_fone']);
    $sql->bindParam('contato_compras_email', $info[$prefix.'contato_compras_email']);
    $sql->bindParam('contato_cobranca', $info[$prefix.'contato_cobranca']);
    $sql->bindParam('contato_cobranca_fone', $info[$prefix.'contato_cobranca_fone']);
    $sql->bindParam('contato_cobranca_email', $info[$prefix.'contato_cobranca_email']);
    $sql->bindParam('contato_cobranca_funcao', $info[$prefix.'contato_cobranca_funcao']);
    $sql->bindParam('grupo', $info[$prefix.'grupo']);
    $sql->bindParam('segmento_mercado', $info[$prefix.'segmento_mercado']);
    $sql->bindParam('regiao', $info[$prefix.'regiao']);
    $sql->bindParam('endereco_cobranca_contato', $info[$prefix.'endereco_cobranca_contato']);
    $sql->bindParam('endereco_cobranca_contato_telefone', $info[$prefix.'endereco_cobranca_contato_telefone']);
    $sql->bindParam('endereco_cobranca_contato_email', $info[$prefix.'endereco_cobranca_contat_email']);
    $sql->bindParam('endereco_entrega_cnpj', $info[$prefix.'endereco_entrega_cnpj']);
    $sql->bindParam('endereco_entrega_ie', $info[$prefix.'endereco_entrega_ie']);
    $sql->bindParam('endereco_entrega_contato', $info[$prefix.'endereco_entrega_contato']);
    $sql->bindParam('endereco_entrega_contato_telefone', $info[$prefix.'endereco_entrega_contato_telefone']);
    $sql->bindParam('endereco_entrega_contato_email', $info[$prefix.'endereco_entrega_contato_email']);
    $sql->bindParam('observacoes', $info[$prefix.'observacoes']);
    $sql->bindParam('restricao', $info[$prefix.'restricao']);
    $sql->bindParam('restricao_texto', $info[$prefix.'restricao_texto']);
    $sql->bindParam('atividade', $info[$prefix.'atividade']);
    $sql->execute();

    }
    catch(PDOException $e){
        $error_message = $e->getMessage();
        echo $error_message;
        exit;
    }


    @$empresas = $_POST['empresas'];
    $sql = "DELETE FROM clientes_empresas WHERE cliente = :id";
    $sql = $con->prepare($sql);
    $sql->bindParam('id', $info[$prefix.'id']);
    $sql->execute();

    if(@is_array($empresas)){
        foreach($empresas as $ep){
            $sql = "INSERT INTO clientes_empresas (cliente, empresa) VALUES (:cliente, :empresa)";
            $sql = $con->prepare($sql);
            $sql->bindParam('cliente', $info[$prefix.'id']);
            $sql->bindParam('empresa', $ep);
            $sql->execute();
        }
    }

    echo 0;
}
exit;
