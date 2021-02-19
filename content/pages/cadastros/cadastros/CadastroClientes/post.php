<?php
@session_start();

header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__.'/../../../../../dist/php/general.inc.php';
ini_set('display_errors', 'on');
$CdRepresentante = $representante_id;
$prefix = "cadastro_clientes_edit_form_";
$con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
if($_POST['action'] == 'GetUserList'){
    // $sql = "SELECT a.* ,
    // b.endereco as endereco_endereco,
    // b.cidade as endereco_cidade,
    // c.nome as endereco_estado
    //
    // FROM clientes a
    // LEFT JOIN dados_enderecos b ON a.endereco=b.id
    // LEFT JOIN dados_estados c ON b.estado=c.id";
    $sql = "SELECT
    a.RzCliente as nome,
    a.CdRepresentante, a.Cnpj_Cnpf,
    a.Cnpj_Cnpf as cpfcnpj,
    a.F_Cidade as endereco_cidade,
    a.F_Estado as endereco_estado
    FROM BusinessCadCliente a
    WHERE a.CdRepresentante = '$CdRepresentante'";

    $sql = $con_sql_server->prepare($sql);
    $sql->execute();
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);


    // LEFT JOIN BusinessCadClienteLC b ON a.CdRepresentante=b.CdRepresentante AND a.Cnpj_Cnpf=b.Cnpj_Cnpf
    // LEFT JOIN BusinessCadEmpresa c ON b.CdEmpresa=c.CdEmpresa
    echo json_encode($row);
}
if($_POST['action'] == 'GetUserData'){
    // $id = (int) $_POST['id'];
    if(@$_POST['CdRepresentante']){
        $CdRepresentante = $_POST['CdRepresentante'];
    }
    else {
        $CdRepresentante = $representante_id;
    }
    $Cnpj_Cnpf = $_POST['Cnpj_Cnpf'];

    // $sql = "SELECT a.*,
    // DATE_FORMAT(a.data_cadastro, '%d-%m-%Y') as data_cadastro,
    // b.cep as endereco_cep,
    // b.endereco as endereco_endereco,
    // b.complemento as endereco_complemento,
    // b.bairro as endereco_bairro,
    // b.cidade as endereco_cidade,
    // b.estado as endereco_estado,
    // b.numero as endereco_numero,
    //
    // c.cep as endereco_cobranca_cep,
    // c.endereco as endereco_cobranca_endereco,
    // c.complemento as endereco_cobranca_complemento,
    // c.bairro as endereco_cobranca_bairro,
    // c.cidade as endereco_cobranca_cidade,
    // c.estado as endereco_cobranca_estado,
    // c.numero as endereco_cobranca_numero,
    //
    // d.cep as endereco_entrega_cep,
    // d.endereco as endereco_entrega_endereco,
    // d.complemento as endereco_entrega_complemento,
    // d.bairro as endereco_entrega_bairro,
    // d.cidade as endereco_entrega_cidade,
    // d.estado as endereco_entrega_estado,
    // d.numero as endereco_entrega_numero
    //
    // FROM clientes a
    // LEFT JOIN dados_enderecos b ON a.endereco=b.id
    // LEFT JOIN dados_enderecos c ON a.endereco_cobranca=c.id
    // LEFT JOIN dados_enderecos d ON a.endereco_entrega=d.id
    // WHERE a.id = :id";
    // $sql = $con->prepare($sql);
    // $sql->bindParam('id', $id);

    $sql = "SELECT b.*,
    a.Cnpj_Cnpf as cpfcnpj,
    a.FsCliente as nome,
    a.RzCliente as razao_social,
    a.Ie_Rg as rgie,
    CONCAT('(',a.DddFax,')', a.Fax) as fax,
    CONCAT('(',a.Ddd1,')', a.Telefone1) as tel1,
    a.CDNATUREZA as natureza_operacao,
    a.F_Cep as endereco_cep,
    a.F_Endereco as endereco_endereco,
    a.F_Complemento as endereco_complemento,
    a.F_Bairro as endereco_bairro,
    a.F_Cidade as endereco_cidade,
    a.F_Estado as endereco_estado,
    a.F_Numero as endereco_numero,
    a.C_Cep as endereco_cobranca_cep,
    a.C_Endereco as endereco_cobranca_endereco,
    a.C_Complemento as endereco_cobranca_complemento,
    a.C_Bairro as endereco_cobranca_bairro,
    a.C_Cidade as endereco_cobranca_cidade,
    a.C_Estado as endereco_cobranca_estado,
    a.C_Numero as endereco_cobranca_numero,
    a.E_cep as endereco_entrega_cep,
    a.E_Endereco as endereco_entrega_endereco,
    a.E_Complemento as endereco_entrega_complemento,
    a.E_Bairro as endereco_entrega_bairro,
    a.E_Cidade as endereco_entrega_cidade,
    a.E_Estado as endereco_entrega_estado,
    a.E_Numero as endereco_entrega_numero,
    a.E_Cnpj as endereco_entrega_cnpj,
    a.E_Ie as endereco_entrega_ie,
    c.FsEmpresa as empresa_nome,
    c.CdEmpresa as empresa

    FROM BusinessCadCliente a
    LEFT JOIN BusinessCadClienteLC b ON a.CdRepresentante=b.CdRepresentante AND a.Cnpj_Cnpf=b.Cnpj_Cnpf
    LEFT JOIN BusinessCadEmpresa c ON b.CdEmpresa=c.CdEmpresa
    WHERE
    a.Cnpj_Cnpf = :Cnpj_Cnpf
    ";
    $sql = $con_sql_server->prepare($sql);
    $sql->bindParam('Cnpj_Cnpf', $Cnpj_Cnpf);
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

    $EnderecosFunc = new Enderecos($con);
    $Valida = new Valida();

    $obrigatorios = array(
        'nome'
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
        $sql = "SELECT * FROM clientes WHERE cpfcnpj = :cpfcnpj";
        $sql = $con->prepare($sql);
        $sql->bindParam('cpfcnpj', $info[$prefix.'cpfcnpj']);
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

    $sql = "INSERT INTO clientes
        (
            endereco,
            endereco_cobranca,
            endereco_entrega,
            nome,
            cpfcnpj,
            rgie,
            suframa,
            suframa_validade,
            desconto,
            natureza_operacao,
            email1,
            tel1,
            fax,
            site,
            contato_compras,
            contato_compras_fone,
            contato_compras_email,
            contato_cobranca,
            contato_cobranca_fone,
            contato_cobranca_email,
            contato_cobranca_funcao,
            grupo,
            segmento_mercado,
            regiao,
            endereco_cobranca_contato,
            endereco_cobranca_contato_telefone,
            endereco_cobranca_contato_email,
            endereco_entrega_cnpj,
            endereco_entrega_ie,
            endereco_entrega_contato,
            endereco_entrega_contato_telefone,
            endereco_entrega_contato_email,
            observacoes,
            atividade,
            restricao,
            restricao_texto,
            data_cadastro
        )

        VALUES (
                :endereco,
                :endereco_cobranca,
                :endereco_entrega,
                :nome,
                :cpfcnpj,
                :rgie,
                :suframa,
                :suframa_validade,
                :desconto,
                :natureza_operacao,
                :email1,
                :tel1,
                :fax,
                :site,
                :contato_compras,
                :contato_compras_fone,
                :contato_compras_email,
                :contato_cobranca,
                :contato_cobranca_fone,
                :contato_cobranca_email,
                :contato_cobranca_funcao,
                :grupo,
                :segmento_mercado,
                :regiao,
                :endereco_cobranca_contato,
                :endereco_cobranca_contato_telefone,
                :endereco_cobranca_contato_email,
                :endereco_entrega_cnpj,
                :endereco_entrega_ie,
                :endereco_entrega_contato,
                :endereco_entrega_contato_telefone,
                :endereco_entrega_contato_email,
                :observacoes,
                :atividade,
                :restricao,
                :restricao_texto,
                NOW()
            )";
    try {
    $sql = $con->prepare($sql);



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

    $insertId = $con->lastInsertId();

    @$empresas = $_POST['empresas'];

    if(@is_array($empresas)){
        foreach($empresas as $ep){
            $sql = "INSERT INTO clientes_empresas (cliente, empresa) VALUES (:cliente, :empresa)";
            $sql = $con->prepare($sql);
            $sql->bindParam('cliente', $insertId);
            $sql->bindParam('empresa', $ep);
            $sql->execute();
        }
    }
    echo 0;
    exit;

}
if($_POST['action'] == 'editar'){

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


        // var_dump($info);
        foreach($info as $key=>$value){
            echo str_replace($prefix, "",$key).'
';
        }
        // var_dump($endereco_entrega);
        // var_dump($endereco_cobranca);
        // var_dump($endereco_principal);

        $Fendereco = $endereco_principal;
        $CEndereco = $endereco_cobranca;
        $EEndereco = $endereco_entrega;

        exit;




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
