<?php
@session_start();

header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__.'/../../../../../dist/php/general.inc.php';
ini_set('display_errors', 'on');
$CdRepresentante = $representante_id;
$prefix = "cadastro_clientes_edit_form_";
$con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
if(@$_POST['action'] == 'VerificaAcessosEdicao'){
    $representante_matriz_id = $representante_id;
    @$CdRepresentante = $_POST['CdRepresentante'];
    $Cnpj_Cnpf = $_POST['Cnpj_Cnpf'];

    $sql = "SELECT a.*, b.CdMunicipio as CdMunicipio,
    c.DsNatureza,
    d.DsSegmento,
    e.DsRegiao



    FROM BusinessCadCliente a
    LEFT JOIN BusinessCadMunicipio b ON a.F_Cidade=b.DsMunicipio
    LEFT JOIN BusinessCadNatOperacao c ON a.CDNATUREZA=c.CdNatureza
    LEFT JOIN BusinessCadSegMercado d ON a.CdSegmento=d.CdSegmento
    LEFT JOIN BusinessCadRegiao e ON a.CdRegiao=e.CdRegiao
    WHERE a.Cnpj_Cnpf = :Cnpj_Cnpf";
    $sql = $con_sql_server->prepare($sql);
    $sql->bindParam('Cnpj_Cnpf', $Cnpj_Cnpf);
    $sql->execute();

    $row = $sql->fetch(PDO::FETCH_ASSOC);
    //Buscas:
    // Natureza Operação, Seg Mercado, Regiao, Municipio,
    //F_Cidade

    $CdNatureza = $row['CDNATUREZA'];
    $CdSegmento = $row['CdSegmento'];
    $CdRegiao = $row['CdRegiao'];
    $CdMunicipio = $row['CdMunicipio'];


    $erro = 0;
    $erro_mensagem = "";

    $SqlServer = new SqlServer($con_sql_server);
    $PermNatureza = $SqlServer->BuscaNaturezaOperacao(0, $representante_matriz_id, $CdNatureza);
    if(@!$PermNatureza && trim($CdNatureza) != ""){
        $erro++;
        $erro_mensagem .= "Representante não tem acesso a natureza de operação ".$CdNatureza." (".$row['DsNatureza'].") <br />";
    }
    $PermSegmento = $SqlServer->BuscaSegmentoMercado($representante_matriz_id, $CdNatureza);
    if(@!$PermSegmento){
        $erro++;
        $erro_mensagem .= "Representante não tem acesso ao Segmento ".$CdSegmento." (".$row['DsSegmento'].") <br />";
    }
    $PermRegiao = $SqlServer->BuscaRegiao($representante_matriz_id, $CdRegiao);
    if(@!$PermRegiao){
        $erro++;
        $erro_mensagem .= "Representante não tem acesso a regiao ".$CdRegiao." (".$row['DsRegiao'].") <br />";
    }
    $PermMunicipio = $SqlServer->VerificaAcessoMunicipio($row['F_Cidade'], $representante_matriz_id);
    if(@!$PermMunicipio){
        $erro++;
        $erro_mensagem .= "Representante não tem acesso ao municipio ".$row["F_Cidade"]." <br />";
    }

    $resposta["erro"] = $erro;
    $resposta["erro_mensagem"] = $erro_mensagem;
    echo json_encode($resposta);
    exit;
    var_dump($row);
    exit;

}
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
    a.CdRegiao as segmento_regiao,
    a.CdSegmento as segmento_mercado,
    a.DtAtivacao as data_cadastro,
    a.PeDesconto as desconto,
    a.Ativo_Inativo_ExCliente as status,
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
            echo str_replace($prefix, "",$key).'e
';
        }
        // var_dump($endereco_entrega);
        // var_dump($endereco_cobranca);
        // var_dump($endereco_principal);

        $Fendereco = $endereco_principal;
        $CEndereco = $endereco_cobranca;
        $EEndereco = $endereco_entrega;


        $campos_sql['CdRepresentante'] = $representante_id;
        $campos_sql['Cnpj_Cnpf'] = $info[$prefix.'cpfcnpj'];
        $campos_sql['FsCliente'] = $info[$prefix.'nome'];
        $campos_sql['RzCliente'] = $info[$prefix.'razao_social'];
        $campos_sql['FlTipo'] = "J";
        $campos_sql['Ie_Rg'] = $info[$prefix.'rgie'];
        $campos_sql['ObsRestricao'] = $info[$prefix.'restricao_texto'];
        $campos_sql['CDNATUREZA'] = $info[$prefix.'natureza_operacao'];
        $campos_sql['DescontaSuframa'] = $info[$prefix.'N'];
        $campos_sql['CdCondPgto'] = null;
        $campos_sql['CdGrupo'] = null;
        $campos_sql['CdTabela'] = null;
        $campos_sql['FlEnvRecEmpresa'] = null;
        $campos_sql['FlEnvRecRepre'] = null;
        $campos_sql['Observacao'] = $info[$prefix.'observacoes'];
        $campos_sql['EstadoForaBrasilFat'] = null;
        $campos_sql['EstadoForaBrasilCob'] = null;
        $campos_sql['EstadoForaBrasilEnt'] = null;
        $campos_sql['CdTransportadora'] = null;
        $campos_sql['CdRegiao'] = $info[$prefix.'segmento_regiao'];
        $campos_sql['CdSegmento'] = $info[$prefix.'segmento_mercado'];
        $campos_sql['Ativo_Inativo_ExCliente'] = $info[$prefix.'status'];
        $campos_sql['RestricaoSN'] = $info[$prefix.'restricao'];
        // $campos_sql['Aviso'] = $info[$prefix.'-']; A FAZER
        $campos_sql['DiaPgto'] = null;
        $campos_sql['ContaContabil'] = null;
        $campos_sql['ClienteBrasil'] =null;
        // $campos_sql['DtAtivacao'] = $info[$prefix.'date']; em cadastro novo
        $campos_sql['DtAtivacaoFim'] = null;
        $campos_sql['DtFundacao'] = $info[$prefix.'data_fundacao'];
        $campos_sql['DtNascimento'] = null;
        $campos_sql['CapitalSocial'] = 0;
        $campos_sql['Redespacho'] = null;
        $campos_sql['E_Email1'] = $info[$prefix.'email'];
        $campos_sql['PeDesconto'] = $info[$prefix.'desconto'];
        $campos_sql['Suframa'] = $info[$prefix.'suframa'];
        $campos_sql['DtValidadeSuframa'] = $info[$prefix.'suframa_validade'];
        $campos_sql['Site'] = $info[$prefix.'site'];
        $campos_sql['OrgaoEmissor'] = $info[$prefix.'null'];

        $campos_sql['Contato1'] = $info[$prefix.'contato_compras'];
        $campos_sql['Funcao1'] = null;
        $campos_sql['Email1'] = $info[$prefix.'contato_compras_email'];
        $campos_sql['Funcao3'] = $info[$prefix.'contato_cobranca_funcao'];
        $campos_sql['Email3'] = $info[$prefix.'contato_cobranca_email'];
        $campos_sql['Contato3'] = $info[$prefix.'contato_cobranca'];
        $campos_sql['Contato2'] = $info[$prefix.'endereco_entrega_contato'];
        $campos_sql['Funcao2'] = null;
        $campos_sql['Email2'] = $info[$prefix.'endereco_entrega_contato_email'];
        $campos_sql['E_Cnpj'] = $info[$prefix.'endereco_entrega_cnpj'];
        $campos_sql['E_Ie'] = $info[$prefix.'endereco_entrega_ie'];


        $campos_sql['F_Cep'] = $info[$prefix.'endereco_cep'];
        $campos_sql['F_Endereco'] = $info[$prefix.'endereco_endereco'];
        $campos_sql['F_Complemento'] = $info[$prefix.'endereco_complemento'];
        $campos_sql['F_Bairro'] = $info[$prefix.'endereco_bairro'];
        $campos_sql['F_Cidade'] = $info[$prefix.'endereco_cidade'];
        $campos_sql['F_Estado'] = $info[$prefix.'endereco_estado'];
        $campos_sql['F_Numero'] = $info[$prefix.'endereco_numero'];
        $campos_sql['C_Cep'] = $info[$prefix.'endereco_cobranca_cep'];
        $campos_sql['C_Endereco'] = $info[$prefix.'endereco_cobranca_endereco'];
        $campos_sql['C_Complemento'] = $info[$prefix.'endereco_cobranca_complemento'];
        $campos_sql['C_Bairro'] = $info[$prefix.'endereco_cobranca_bairro'];
        $campos_sql['C_Cidade'] = $info[$prefix.'endereco_cobranca_cidade'];
        $campos_sql['C_Estado'] = $info[$prefix.'endereco_cobranca_estado'];
        $campos_sql['C_Numero'] = $info[$prefix.'endereco_cobranca_numero'];
        $campos_sql['E_cep'] = $info[$prefix.'endereco_entrega_cep'];
        $campos_sql['E_Endereco'] = $info[$prefix.'endereco_entrega_endereco'];
        $campos_sql['E_Complemento'] = $info[$prefix.'endereco_entrega_complemento'];
        $campos_sql['E_Bairro'] = $info[$prefix.'endereco_entrega_bairro'];
        $campos_sql['E_Cidade'] = $info[$prefix.'endereco_entrega_cidade'];
        $campos_sql['E_Estado'] = $info[$prefix.'endereco_entrega_estado'];
        $campos_sql['E_Numero'] = $info[$prefix.'endereco_entrega_numero'];




        $campos_sql['Ddd3'] = substr($info[$prefix.'contato_cobranca_fone'], 1, 2);
        $campos_sql['Telefone3'] = trim(substr($info[$prefix.'contato_cobranca_fone'], 5, strlen($info[$prefix.'contato_cobranca_fone'])));
        $campos_sql['Ddd2'] = substr($info[$prefix.'endereco_entrega_contato_telefone'], 1, 2);
        $campos_sql['Telefone2'] = trim(substr($info[$prefix.'endereco_entrega_contato_telefone'], 5, strlen($info[$prefix.'endereco_entrega_contato_telefone'])));
        $campos_sql['Ddd1'] = substr($info[$prefix.'contato_compras_fone'], 1, 2);
        $campos_sql['Telefone1'] = trim(substr($info[$prefix.'contato_compras_fone'], 5, strlen($info[$prefix.'contato_compras_fone'])));
        $campos_sql['E_Ddd1'] = substr($info[$prefix.'tel1'], 1, 2);
        $campos_sql['E_Telefone1'] = trim(substr($info[$prefix.'tel1'], 5, strlen($info[$prefix.'tel1'])));
        $campos_sql['DddFax'] = substr($info[$prefix.'fax'], 1, 2);
        $campos_sql['Fax'] = trim(substr($info[$prefix.'fax'], 5, strlen($info[$prefix.'fax'])));





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
