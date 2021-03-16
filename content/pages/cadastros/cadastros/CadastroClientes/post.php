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

    @$CdNatureza = $row['CDNATUREZA'];
    @$CdSegmento = $row['CdSegmento'];
    @$CdRegiao = $row['CdRegiao'];
    @$CdMunicipio = $row['CdMunicipio'];


    $erro = 0;
    $erro_mensagem = "";

    $SqlServer = new SqlServer($con_sql_server);
    $PermNatureza = $SqlServer->BuscaNaturezaOperacao(0, $representante_matriz_id, $CdNatureza);
    if(@!$PermNatureza && trim($CdNatureza) != ""){
        $erro++;
        $erro_mensagem .= "Representante não tem acesso a natureza de operação ".$CdNatureza." (".$row['DsNatureza'].") <br />";
    }
    $PermSegmento = $SqlServer->BuscaSegmentoMercado($representante_matriz_id, $CdSegmento);
    if(@!$PermSegmento){
        $erro++;
        $erro_mensagem .= "Representante não tem acesso ao Segmento ".$CdSegmento." (".$row['DsSegmento'].") <br />";
    }
    $PermRegiao = $SqlServer->BuscaRegiao($representante_matriz_id, $CdRegiao);
    if(@!$PermRegiao){
        $erro++;
        $erro_mensagem .= "Representante não tem acesso a regiao ".$CdRegiao." (".$row['DsRegiao'].") <br />";
    }
    $PermMunicipio = $SqlServer->VerificaAcessoMunicipio($row['F_Cidade'], $representante_matriz_id, $row['F_Estado']);
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
    -- LEFT JOIN BusinessCadClienteLC b ON a.Cnpj_Cnpf=b.Cnpj_Cnpf
    WHERE a.CdRepresentante = '$CdRepresentante'
    -- OR b.CdRepresentante = '$CdRepresentante'

    ";

    $sql = $con_sql_server->prepare($sql);
    $sql->execute();
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);





    // LEFT JOIN BusinessCadClienteLC b ON a.CdRepresentante=b.CdRepresentante AND a.Cnpj_Cnpf=b.Cnpj_Cnpf
    // LEFT JOIN BusinessCadEmpresa c ON b.CdEmpresa=c.CdEmpresa
    echo json_encode(utf8ize($row));
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
    CONCAT('(',a.Ddd1,')', a.Telefone1) as contato_cobranca_fone,
    a.Contato1 as contato_cobranca,
    CONCAT('(',a.F_Ddd1,')', a.F_Telefone1) as contato_compras_fone,
    a.F_Contato1 as contato_compras,
    CONCAT('(',a.C_Ddd1,')', a.C_Telefone1) as endereco_cobranca_contato_telefone,
    a.C_Contato1 as endereco_cobranca_contato,
    CONCAT('(',a.E_Ddd1,')', a.E_Telefone1) as endereco_entrega_contato_telefone,
    Suframa as suframa,
    FORMAT(DtValidadeSuframa, 'yyyy-MM-dd') as suframa_validade,
    Site as site,
    F_Email1 as contato_compras_email,
    Email1 as contato_cobranca_email,
    Funcao1 as contato_cobranca_funcao,
    C_Email1 as endereco_cobranca_contato_email,
    E_Email1 as endereco_entrega_contato_email,
    ObsRestricao as restricao_texto,
    Observacao as observacoes,
    RestricaoSN as restricao,
    a.E_Contato1 as endereco_entrega_contato,
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
    a.E_Cep as endereco_entrega_cep,
    a.E_Endereco as endereco_entrega_endereco,
    a.E_Complemento as endereco_entrega_complemento,
    a.E_Bairro as endereco_entrega_bairro,
    a.E_Cidade as endereco_entrega_cidade,
    a.E_Estado as endereco_entrega_estado,
    a.E_Numero as endereco_entrega_numero,
    a.E_Cnpj as endereco_entrega_cnpj,
    a.E_Ie as endereco_entrega_ie,
    a.CdGrupo as grupo,
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



    $sql = "SELECT *, FORMAT(DtNascimento, 'yyyy-MM-dd') as DtNascimento FROM BusinessCadClienteSocio WHERE Cnpj_Cnpf = :Cnpj_Cnpf AND CdRepresentante = :CdRepresentante";
    $sql = $con_sql_server->prepare($sql);
    $sql->bindParam('Cnpj_Cnpf', $Cnpj_Cnpf);
    $sql->bindParam('CdRepresentante', $CdRepresentante);

    $sql->execute();

    $row['socios'] = $sql->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT *, FORMAT(DtNascimento, 'yyyy-MM-dd') as DtNascimento FROM BusinessCadClienteGerSet WHERE Cnpj_Cnpf = :Cnpj_Cnpf AND CdRepresentante = :CdRepresentante";
    $sql = $con_sql_server->prepare($sql);
    $sql->bindParam('Cnpj_Cnpf', $Cnpj_Cnpf);
    $sql->bindParam('CdRepresentante', $CdRepresentante);

    $sql->execute();

    $row['gerentes'] = $sql->fetchAll(PDO::FETCH_ASSOC);
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

    var_dump($_POST);
    $cont_socios = 0;
    $cont_gerentes = 0;
    $cont = 0;
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

            if($dd['name'] == "cadastro_clientes_edit_form_socios_nome" && $dd['value'] != ""){
                $socios[$cont_socios]['NmSocio'] = $dd['value'];
                $socios[$cont_socios]['DtNascimento'] = $dados[$cont+1]['value'];
                $socios[$cont_socios]['FlEstCivil'] = $dados[$cont+2]['value'];
                $socios[$cont_socios]['QtdeFilhos'] = $dados[$cont+3]['value'];
                $socios[$cont_socios]['BebidaPreferida'] = $dados[$cont+4]['value'];
                $socios[$cont_socios]['TimeFutebol'] = $dados[$cont+5]['value'];
                $socios[$cont_socios]['EsportePratica'] = $dados[$cont+6]['value'];
                $socios[$cont_socios]['CdSequencia'] = str_pad($cont_socios+1, 2, "0", STR_PAD_LEFT);


                $cont_socios++;
            }


            if($dd['name'] == "cadastro_clientes_edit_form_gerentes_nome" && $dd['value'] != ""){
                $gerentes[$cont_gerentes]['NmGerente'] = $dd['value'];
                $gerentes[$cont_gerentes]['DtNascimento'] = $dados[$cont+1]['value'];
                $gerentes[$cont_gerentes]['FlEstCivil'] = $dados[$cont+2]['value'];
                $gerentes[$cont_gerentes]['QtdeFilhos'] = $dados[$cont+3]['value'];
                $gerentes[$cont_gerentes]['BebidaPreferida'] = $dados[$cont+4]['value'];
                $gerentes[$cont_gerentes]['TimeFutebol'] = $dados[$cont+5]['value'];
                $gerentes[$cont_gerentes]['EsportePratica'] = $dados[$cont+6]['value'];
                $gerentes[$cont_gerentes]['CdSequencia'] = str_pad($cont_gerentes+1, 2, "0", STR_PAD_LEFT);


                $cont_gerentes++;
            }
            $cont++;

        }




        @$campos_sql['CdRepresentante'] = $representante_id;
        @$campos_sql['CdRepresentante_old'] = $info[$prefix.'CdRepresentante'];
        @$campos_sql['Cnpj_Cnpf'] = @$info[$prefix.'cpfcnpj'];
        @$campos_sql['FsCliente'] = $info[$prefix.'nome'];
        @$campos_sql['RzCliente'] = @$info[$prefix.'razao_social'];
        @$campos_sql['FlTipo'] = "J";
        @$campos_sql['Ie_Rg'] = $info[$prefix.'rgie'];
        @$campos_sql['ObsRestricao'] = $info[$prefix.'restricao_texto'];
        @$campos_sql['CDNATUREZA'] = $info[$prefix.'natureza_operacao'];
        @$campos_sql['CdCondPgto'] = null;
        @$campos_sql['CdGrupo'] = @$info[$prefix.'grupo'];
        @$campos_sql['CdTabela'] = null;
        @$campos_sql['FlEnvRecEmpresa'] = null;
        @$campos_sql['FlEnvRecRepre'] = null;
        @$campos_sql['Observacao'] = @$info[$prefix.'observacoes'];
        @$campos_sql['EstadoForaBrasilFat'] = null;
        @$campos_sql['EstadoForaBrasilCob'] = null;
        @$campos_sql['EstadoForaBrasilEnt'] = null;
        @$campos_sql['CdTransportadora'] = null;
        @$campos_sql['CdRegiao'] = $info[$prefix.'segmento_regiao'];
        @$campos_sql['CdSegmento'] = $info[$prefix.'segmento_mercado'];
        @$campos_sql['Ativo_Inativo_ExCliente'] = $info[$prefix.'status'];
        @$campos_sql['RestricaoSN'] = $info[$prefix.'restricao'];
        @$campos_sql['DiaPgto'] = null;
        @$campos_sql['ContaContabil'] = null;
        @$campos_sql['ClienteBrasil'] =null;
        @$campos_sql['DtAtivacaoFim'] = null;
        @$campos_sql['DtFundacao'] = $info[$prefix.'data_fundacao'];
        @$campos_sql['DtNascimento'] = null;
        @$campos_sql['CapitalSocial'] = 0;
        @$campos_sql['Redespacho'] = null;
        @$campos_sql['E_Email1'] = $info[$prefix.'email'];
        @$campos_sql['PeDesconto'] = $info[$prefix.'desconto'];
        @$campos_sql['Suframa'] = $info[$prefix.'suframa'];
        @$campos_sql['DtValidadeSuframa'] = $info[$prefix.'suframa_validade'];
        @$campos_sql['Site'] = $info[$prefix.'site'];
        @$campos_sql['OrgaoEmissor'] = null;

        @$campos_sql['Contato1'] = $info[$prefix.'contato_cobranca'];
        @$campos_sql['Funcao1'] = null;
        @$campos_sql['Email1'] = $info[$prefix.'contato_compras_email'];
        @$campos_sql['Funcao3'] = $info[$prefix.'contato_cobranca_funcao'];
        @$campos_sql['Email3'] = $info[$prefix.'contato_cobranca_email'];
        @$campos_sql['Contato3'] = $info[$prefix.'contato_cobranca'];
        @$campos_sql['Contato2'] = $info[$prefix.'endereco_entrega_contato'];
        @$campos_sql['Funcao2'] = null;
        @$campos_sql['Email2'] = $info[$prefix.'endereco_entrega_contato_email'];
        @$campos_sql['E_Cnpj'] = $info[$prefix.'endereco_entrega_cnpj'];
        @$campos_sql['E_Ie'] = $info[$prefix.'endereco_entrega_ie'];


        @$campos_sql['F_Cep'] = $info[$prefix.'endereco_cep'];
        @$campos_sql['F_Endereco'] = $info[$prefix.'endereco_endereco'];
        @$campos_sql['F_Complemento'] = $info[$prefix.'endereco_complemento'];
        @$campos_sql['F_Bairro'] = $info[$prefix.'endereco_bairro'];
        @$campos_sql['F_Cidade'] = $info[$prefix.'endereco_cidade'];
        @$campos_sql['F_Estado'] = $info[$prefix.'endereco_estado'];
        @$campos_sql['F_Numero'] = $info[$prefix.'endereco_numero'];
        @$campos_sql['C_Cep'] = $info[$prefix.'endereco_cobranca_cep'];
        @$campos_sql['C_Endereco'] = $info[$prefix.'endereco_cobranca_endereco'];
        @$campos_sql['C_Complemento'] = $info[$prefix.'endereco_cobranca_complemento'];
        @$campos_sql['C_Bairro'] = $info[$prefix.'endereco_cobranca_bairro'];
        @$campos_sql['C_Cidade'] = $info[$prefix.'endereco_cobranca_cidade'];
        @$campos_sql['C_Estado'] = $info[$prefix.'endereco_cobranca_estado'];
        @$campos_sql['C_Numero'] = $info[$prefix.'endereco_cobranca_numero'];
        @$campos_sql['E_Cep'] = $info[$prefix.'endereco_entrega_cep'];
        @$campos_sql['E_Endereco'] = $info[$prefix.'endereco_entrega_endereco'];
        @$campos_sql['E_Complemento'] = $info[$prefix.'endereco_entrega_complemento'];
        @$campos_sql['E_Bairro'] = $info[$prefix.'endereco_entrega_bairro'];
        @$campos_sql['E_Cidade'] = $info[$prefix.'endereco_entrega_cidade'];
        @$campos_sql['E_Estado'] = $info[$prefix.'endereco_entrega_estado'];
        @$campos_sql['E_Numero'] = $info[$prefix.'endereco_entrega_numero'];




        @$campos_sql['F_Ddd1'] = substr($info[$prefix.'contato_compras_fone'], 1, 2);
        @$campos_sql['F_Telefone1'] = trim(substr($info[$prefix.'contato_compras_fone'], 5, strlen($info[$prefix.'contato_compras_fone'])));
        @$campos_sql['F_Contato1'] = $info[$prefix.'contato_compras'];
        @$campos_sql['F_Email1'] = $info[$prefix.'contato_compras_email'];



        @$campos_sql['E_Ddd1'] = substr($info[$prefix.'endereco_entrega_contato_telefone'], 1, 2);
        @$campos_sql['E_Telefone1'] = trim(substr($info[$prefix.'endereco_entrega_contato_telefone'], 5, strlen($info[$prefix.'endereco_entrega_contato_telefone'])));
        @$campos_sql['E_Contato1'] = $info[$prefix.'endereco_entrega_contato'];
        @$campos_sql['E_Email1'] = $info[$prefix.'endereco_entrega_contato_email'];

        @$campos_sql['Ddd1'] = substr($info[$prefix.'contato_cobranca_fone'], 1, 2);
        @$campos_sql['Telefone1'] = trim(substr($info[$prefix.'contato_cobranca_fone'], 5, strlen($info[$prefix.'contato_cobranca_fone'])));
        @$campos_sql['Email1'] =  $info[$prefix.'contato_cobranca_email'];
        @$campos_sql['Funcao1'] =  $info[$prefix.'contato_cobranca_funcao'];

        @$campos_sql['C_Ddd1'] = substr($info[$prefix.'endereco_cobranca_contato_telefone'], 1, 2);
        @$campos_sql['C_Telefone1'] = trim(substr($info[$prefix.'endereco_cobranca_contato_telefone'], 5, strlen($info[$prefix.'endereco_cobranca_contato_telefone'])));
        @$campos_sql['C_Contato1'] = $info[$prefix.'endereco_cobranca_contato'];
        @$campos_sql['C_Email1'] = $info[$prefix.'endereco_cobranca_contato_email'];

        @$campos_sql['RestricaoSN'] = $_POST['campo_restricao'];

        @$campos_sql['DddFax'] = substr($info[$prefix.'fax'], 1, 2);
        @$campos_sql['Fax'] = trim(substr($info[$prefix.'fax'], 5, strlen($info[$prefix.'fax'])));
        // var_dump($campos_sql['Ddd1']);
        // var_dump($campos_sql['Telefone1']);
        // exit;

        @$campos_sql['ObsRestricao'] = $info[$prefix.'restricao_texto'];
        @$campos_sql['Observacao'] = $info[$prefix.'observacoes'];


        $error_message = "";
        $error = 0;
        if(@is_array($obrigatorios)){
            foreach($obrigatorios as $ob){
                if(!isset($info[$prefix.$ob]) or $info[$prefix.$ob] == ""){
                    $error++;
                    $error_message .= "$ob é requerido <br />";
                }
            }
        }

        if($error > 0){
            echo $error_message;
            exit;
        }




    $info[$prefix.'cpfcnpj'] = trim(str_replace('.', '', str_replace('-', '', str_replace('/', '', $info[$prefix.'cpfcnpj']))));

    $cpfcnpj = $info[$prefix.'cpfcnpj'];
    $Valida = new Valida();

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





    $sql = "INSERT INTO BusinessCadCliente
        (
            CdRepresentante,
            Cnpj_Cnpf,
            FsCliente,
            RzCliente,
            FlTipo,
            CDNATUREZA,
            CdSegmento,
            Ie_Rg,
            C_Complemento,
            E_Numero,
            E_Complemento,
            F_Numero,
            F_Complemento,
            C_Numero,
            Suframa,
            DtValidadeSuframa,
            CdGrupo,
            CdRegiao,
            Aviso,
            E_Ddd1,
            E_Telefone1,
            E_Email1,
            E_Cidade,
            E_Estado,
            E_Cep,
            E_Cnpj,
            E_Ie,
            E_Contato1,
            C_Ddd1,
            C_Telefone1,
            C_Email1,
            E_Endereco,
            E_Bairro,
            E_CdMunicipio,
            C_Bairro,
            C_CdMunicipio,
            C_Cidade,
            C_Estado,
            C_Cep,
            C_Contato1,
            F_Cep,
            F_Contato1,
            F_Ddd1,
            F_Telefone1,
            F_Email1,
            C_Endereco,
            Site,
            F_Endereco,
            F_Bairro,
            F_CdMunicipio,
            F_Cidade,
            F_Estado,
            Funcao3,
            Ddd3,
            Telefone3,
            Email3,
            DddFax,
            Fax,
            Contato2,
            Funcao2,
            Ddd2,
            Telefone2,
            Email2,
            Contato3,
            Contato1,
            Funcao1,
            Ddd1,
            Telefone1,
            Email1,
            ObsRestricao,
            Observacao,
            RestricaoSN,
            DtAtivacao,
            Ativo_Inativo_ExCliente
        )

        VALUES (
            :CdRepresentante,
            :Cnpj_Cnpf,
            :FsCliente,
            :RzCliente,
            :FlTipo,
            :CDNATUREZA,
            :CdSegmento,
            :Ie_Rg,
            :C_Complemento,
            :E_Numero,
            :E_Complemento,
            :F_Numero,
            :F_Complemento,
            :C_Numero,
            :Suframa,
            :DtValidadeSuframa,
            :CdGrupo,
            :CdRegiao,
            :Aviso,
            :E_Ddd1,
            :E_Telefone1,
            :E_Email1,
            :E_Cidade,
            :E_Estado,
            :E_Cep,
            :E_Cnpj,
            :E_Ie,
            :E_Contato1,
            :C_Ddd1,
            :C_Telefone1,
            :C_Email1,
            :E_Endereco,
            :E_Bairro,
            :E_CdMunicipio,
            :C_Bairro,
            :C_CdMunicipio,
            :C_Cidade,
            :C_Estado,
            :C_Cep,
            :C_Contato1,
            :F_Cep,
            :F_Contato1,
            :F_Ddd1,
            :F_Telefone1,
            :F_Email1,
            :C_Endereco,
            :Site,
            :F_Endereco,
            :F_Bairro,
            :F_CdMunicipio,
            :F_Cidade,
            :F_Estado,
            :Funcao3,
            :Ddd3,
            :Telefone3,
            :Email3,
            :DddFax,
            :Fax,
            :Contato2,
            :Funcao2,
            :Ddd2,
            :Telefone2,
            :Email2,
            :Contato3,
            :Contato1,
            :Funcao1,
            :Ddd1,
            :Telefone1,
            :Email1,
            :ObsRestricao,
            :Observacao,
            :RestricaoSN,
            getdate(),
            :Ativo_Inativo_ExCliente
            )";
    try {
    $sql = $con->prepare($sql);



    $sql->bindParam('CdRepresentante', $campos_sql['CdRepresentante']);
    $sql->bindParam('Cnpj_Cnpf', $campos_sql['Cnpj_Cnpf']);
    $sql->bindParam('Ie_Rg',$campos_sql['Ie_Rg']);
    $sql->bindParam('FsCliente',$campos_sql['FsCliente']);
    $sql->bindParam('RzCliente',$campos_sql['RzCliente']);
    $sql->bindParam('FlTipo',$campos_sql['FlTipo']);
    $sql->bindParam('CDNATUREZA',$campos_sql['CDNATUREZA']);
    $sql->bindParam('CdSegmento',$campos_sql['CdSegmento']);
    $sql->bindParam('C_Complemento',$campos_sql['C_Complemento']);
    $sql->bindParam('E_Numero',$campos_sql['E_Numero']);
    $sql->bindParam('E_Complemento',$campos_sql['E_Complemento']);
    $sql->bindParam('F_Numero',$campos_sql['F_Numero']);
    $sql->bindParam('F_Complemento',$campos_sql['F_Complemento']);
    $sql->bindParam('C_Numero',$campos_sql['C_Numero']);
    $sql->bindParam('Suframa',$campos_sql['Suframa']);
    $sql->bindParam('DtValidadeSuframa',$campos_sql['DtValidadeSuframa']);
    $sql->bindParam('CdGrupo',$campos_sql['CdGrupo']);


    $sql->bindParam('CdRegiao',$campos_sql['CdRegiao']);
    $sql->bindParam('Aviso',$campos_sql['Aviso']);
    $sql->bindParam('E_Ddd1',$campos_sql['E_Ddd1']);
    $sql->bindParam('E_Telefone1',$campos_sql['E_Telefone1']);
    $sql->bindParam('E_Email1',$campos_sql['E_Email1']);
    $sql->bindParam('E_Cidade',$campos_sql['E_Cidade']);
    $sql->bindParam('E_Estado',$campos_sql['E_Estado']);
    $sql->bindParam('E_Cep',$campos_sql['E_Cep']);
    $sql->bindParam('E_Cnpj',$campos_sql['E_Cnpj']);
    $sql->bindParam('E_Ie',$campos_sql['E_Ie']);
    $sql->bindParam('E_Contato1',$campos_sql['E_Contato1']);
    $sql->bindParam('C_Ddd1',$campos_sql['C_Ddd1']);
    $sql->bindParam('C_Telefone1',$campos_sql['C_Telefone1']);
    $sql->bindParam('C_Email1',$campos_sql['C_Email1']);
    $sql->bindParam('E_Endereco',$campos_sql['E_Endereco']);
    $sql->bindParam('E_Bairro',$campos_sql['E_Bairro']);
    $sql->bindParam('E_CdMunicipio',$campos_sql['E_CdMunicipio']);
    $sql->bindParam('C_Bairro',$campos_sql['C_Bairro']);
    $sql->bindParam('C_CdMunicipio',$campos_sql['C_CdMunicipio']);
    $sql->bindParam('C_Cidade',$campos_sql['C_Cidade']);
    $sql->bindParam('C_Estado',$campos_sql['C_Estado']);
    $sql->bindParam('C_Cep',$campos_sql['C_Cep']);
    $sql->bindParam('C_Contato1',$campos_sql['C_Contato1']);
    $sql->bindParam('F_Cep',$campos_sql['F_Cep']);
    $sql->bindParam('F_Contato1',$campos_sql['F_Contato1']);
    $sql->bindParam('F_Ddd1',$campos_sql['F_Ddd1']);
    $sql->bindParam('F_Telefone1',$campos_sql['F_Telefone1']);
    $sql->bindParam('F_Email1',$campos_sql['F_Email1']);
    $sql->bindParam('C_Endereco',$campos_sql['C_Endereco']);
    $sql->bindParam('Site',$campos_sql['Site']);
    $sql->bindParam('F_Endereco',$campos_sql['F_Endereco']);
    $sql->bindParam('F_Bairro',$campos_sql['F_Bairro']);
    $sql->bindParam('F_CdMunicipio',$campos_sql['F_CdMunicipio']);
    $sql->bindParam('F_Cidade',$campos_sql['F_Cidade']);
    $sql->bindParam('F_Estado',$campos_sql['F_Estado']);
    $sql->bindParam('Funcao3',$campos_sql['Funcao3']);
    $sql->bindParam('Ddd3',$campos_sql['Ddd3']);
    $sql->bindParam('Telefone3',$campos_sql['Telefone3']);
    $sql->bindParam('Email3',$campos_sql['Email3']);
    $sql->bindParam('DddFax',$campos_sql['DddFax']);
    $sql->bindParam('Fax',$campos_sql['Fax']);
    $sql->bindParam('Contato2',$campos_sql['Contato2']);
    $sql->bindParam('Funcao2',$campos_sql['Funcao2']);
    $sql->bindParam('Ddd2',$campos_sql['Ddd2']);
    $sql->bindParam('Telefone2',$campos_sql['Telefone2']);
    $sql->bindParam('Email2',$campos_sql['Email2']);
    $sql->bindParam('Contato3',$campos_sql['Contato3']);
    $sql->bindParam('Contato1',$campos_sql['Contato1']);
    $sql->bindParam('Funcao1',$campos_sql['Funcao1']);
    $sql->bindParam('Ddd1',$campos_sql['Ddd1']);
    $sql->bindParam('Telefone1',$campos_sql['Telefone1']);
    $sql->bindParam('Email1',$campos_sql['Email1']);
    $sql->bindParam('ObsRestricao',$campos_sql['ObsRestricao']);
    $sql->bindParam('Observacao',$campos_sql['Observacao']);
    $sql->bindParam('RestricaoSN',$campos_sql['RestricaoSN']);
    $sql->bindParam('Ativo_Inativo_ExCliente',$campos_sql['Ativo_Inativo_ExCliente']);

    $sql->execute();

    }
    catch(Exception $e){
        $error_message = $e->getMessage();
        echo $error_message;
        exit;
    }
    catch(PDOException $ex){
        $error_message = $ex->getMessage();
        echo $error_message;
        exit;
    }

    // $insertId = $con->lastInsertId();

    @$empresas = $_POST['empresas'];

    if(@is_array($empresas)){
        foreach($empresas as $ep){
            $sql = "INSERT INTO BusinessCadClienteLC
            (CdRepresentante, CdEmpresa, Cnpj_Cnpf, LimiteCredito, VlUltCompra, FlEnvRecRepre)
            VALUES
            (:CdRepresentante, :CdEmpresa, :Cnpj_Cnpf, 0, 0, 'S')";

            try {


                $sql = $con->prepare($sql);
                $sql->bindParam('CdRepresentante', $representante_id);
                $sql->bindParam('CdEmpresa', $ep);
                $sql->bindParam('Cnpj_Cnpf', $campos_sql['Cnpj_Cnpf']);

                @$sql->execute();
            }
            catch(Exception $e){
                $error_message = $e->getMessage();
                // echo $error_message;
                //exit;
            }
            catch(PDOException $ex){
                $error_message = $ex->getMessage();
                // echo $error_message;
                //exit;
            }
        }
    }


    $sql = "DELETE FROM BusinessCadClienteSocio WHERE
    CdRepresentante = :CdRepresentante AND
    Cnpj_Cnpf = :Cnpj_Cnpf";
    try {
        $sql = $con->prepare($sql);
        $sql->bindParam('CdRepresentante', $campos_sql['CdRepresentante']);
        $sql->bindParam('Cnpj_Cnpf', $campos_sql['Cnpj_Cnpf']);
        $sql->execute();
    }
    catch(Exception $e){
        $error_message = $e->getMessage();
        echo $error_message;
        exit;
    }
    catch(PDOException $ex){
        $error_message = $ex->getMessage();
        echo $error_message;
        exit;
    }

    if(@is_array($socios)){
        foreach($socios as $sc){
            $sql = "INSERT INTO BusinessCadClienteSocio
            (
                CdRepresentante,
                Cnpj_Cnpf,
                CdSequencia,
                NmSocio,
                DtNascimento,
                FlEstCivil,
                QtdeFilhos,
                BebidaPreferida,
                TimeFutebol,
                EsportePratica
            )
            VALUES (
                :CdRepresentante,
                :Cnpj_Cnpf,
                :CdSequencia,
                :NmSocio,
                :DtNascimento,
                :FlEstCivil,
                :QtdeFilhos,
                :BebidaPreferida,
                :TimeFutebol,
                :EsportePratica
            )
            ";
            try {
                $sql = $con->prepare($sql);
                $sql->bindParam('CdRepresentante', $campos_sql['CdRepresentante']);
                $sql->bindParam('Cnpj_Cnpf', $campos_sql['Cnpj_Cnpf']);
                $sql->bindParam('CdSequencia', $sc['CdSequencia']);
                $sql->bindParam('NmSocio', $sc['NmSocio']);
                $sql->bindParam('DtNascimento', $sc['DtNascimento']);
                $sql->bindParam('FlEstCivil', $sc['FlEstCivil']);
                $sql->bindParam('QtdeFilhos', $sc['QtdeFilhos']);
                $sql->bindParam('BebidaPreferida', $sc['BebidaPreferida']);
                $sql->bindParam('TimeFutebol', $sc['TimeFutebol']);
                $sql->bindParam('EsportePratica', $sc['EsportePratica']);
                $sql->execute();
            }
            catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
            catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            }
        }
    }


    if(@is_array($gerentes)){
        foreach($gerentes as $sc){
            $sql = "INSERT INTO BusinessCadClienteGerSet
            (
                CdRepresentante,
                Cnpj_Cnpf,
                CdSequencia,
                NmGerente,
                DtNascimento,
                FlEstCivil,
                QtdeFilhos,
                BebidaPreferida,
                TimeFutebol,
                EsportePratica
            )
            VALUES (
                :CdRepresentante,
                :Cnpj_Cnpf,
                :CdSequencia,
                :NmGerente,
                :DtNascimento,
                :FlEstCivil,
                :QtdeFilhos,
                :BebidaPreferida,
                :TimeFutebol,
                :EsportePratica
            )
            ";
            try {
                $sql = $con->prepare($sql);
                $sql->bindParam('CdRepresentante', $campos_sql['CdRepresentante']);
                $sql->bindParam('Cnpj_Cnpf', $campos_sql['Cnpj_Cnpf']);
                $sql->bindParam('CdSequencia', $sc['CdSequencia']);
                $sql->bindParam('NmGerente', $sc['NmGerente']);
                $sql->bindParam('DtNascimento', $sc['DtNascimento']);
                $sql->bindParam('FlEstCivil', $sc['FlEstCivil']);
                $sql->bindParam('QtdeFilhos', $sc['QtdeFilhos']);
                $sql->bindParam('BebidaPreferida', $sc['BebidaPreferida']);
                $sql->bindParam('TimeFutebol', $sc['TimeFutebol']);
                $sql->bindParam('EsportePratica', $sc['EsportePratica']);
                $sql->execute();
            }
            catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
            catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            }
        }
    }
    echo 0;
    exit;

}
if($_POST['action'] == 'editar'){
    // var_dump($_POST);
    // exit;
    $cont_socios = 0;
    $cont = 0;
    $cont_gerentes = 0;
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

            if($dd['name'] == "cadastro_clientes_edit_form_socios_nome" && $dd['value'] != ""){
                $socios[$cont_socios]['NmSocio'] = $dd['value'];
                $socios[$cont_socios]['DtNascimento'] = $dados[$cont+1]['value'];
                $socios[$cont_socios]['FlEstCivil'] = $dados[$cont+2]['value'];
                $socios[$cont_socios]['QtdeFilhos'] = $dados[$cont+3]['value'];
                $socios[$cont_socios]['BebidaPreferida'] = $dados[$cont+4]['value'];
                $socios[$cont_socios]['TimeFutebol'] = $dados[$cont+5]['value'];
                $socios[$cont_socios]['EsportePratica'] = $dados[$cont+6]['value'];
                $socios[$cont_socios]['CdSequencia'] = str_pad($cont_socios+1, 2, "0", STR_PAD_LEFT);


                $cont_socios++;
            }

            if($dd['name'] == "cadastro_clientes_edit_form_gerentes_nome" && $dd['value'] != ""){
                $gerentes[$cont_gerentes]['NmGerente'] = $dd['value'];
                $gerentes[$cont_gerentes]['DtNascimento'] = $dados[$cont+1]['value'];
                $gerentes[$cont_gerentes]['FlEstCivil'] = $dados[$cont+2]['value'];
                $gerentes[$cont_gerentes]['QtdeFilhos'] = $dados[$cont+3]['value'];
                $gerentes[$cont_gerentes]['BebidaPreferida'] = $dados[$cont+4]['value'];
                $gerentes[$cont_gerentes]['TimeFutebol'] = $dados[$cont+5]['value'];
                $gerentes[$cont_gerentes]['EsportePratica'] = $dados[$cont+6]['value'];
                $gerentes[$cont_gerentes]['CdSequencia'] = str_pad($cont_gerentes+1, 2, "0", STR_PAD_LEFT);


                $cont_gerentes++;
            }
            $cont++;

        }


//         var_dump($info);
//         foreach($info as $key=>$value){
//             echo str_replace($prefix, "",$key).'e
// ';
//         }
        // var_dump($endereco_entrega);
        // var_dump($endereco_cobranca);
        // var_dump($endereco_principal);

        $Fendereco = $endereco_principal;
        $CEndereco = $endereco_cobranca;
        $EEndereco = $endereco_entrega;


        @$campos_sql['CdRepresentante'] = $representante_id;
        @$campos_sql['CdRepresentante_old'] = $info[$prefix.'CdRepresentante'];
        @$campos_sql['Cnpj_Cnpf'] = @$info[$prefix.'cpfcnpj'];
        @$campos_sql['FsCliente'] = $info[$prefix.'nome'];
        @$campos_sql['RzCliente'] = @$info[$prefix.'razao_social'];
        @$campos_sql['FlTipo'] = "J";
        @$campos_sql['Ie_Rg'] = $info[$prefix.'rgie'];
        @$campos_sql['ObsRestricao'] = $info[$prefix.'restricao_texto'];
        @$campos_sql['CDNATUREZA'] = $info[$prefix.'natureza_operacao'];
        @$campos_sql['CdCondPgto'] = null;
        @$campos_sql['CdGrupo'] = @$info[$prefix.'grupo'];
        @$campos_sql['CdTabela'] = null;
        @$campos_sql['FlEnvRecEmpresa'] = null;
        @$campos_sql['FlEnvRecRepre'] = null;
        @$campos_sql['Observacao'] = @$info[$prefix.'observacoes'];
        @$campos_sql['EstadoForaBrasilFat'] = null;
        @$campos_sql['EstadoForaBrasilCob'] = null;
        @$campos_sql['EstadoForaBrasilEnt'] = null;
        @$campos_sql['CdTransportadora'] = null;
        @$campos_sql['CdRegiao'] = $info[$prefix.'segmento_regiao'];
        @$campos_sql['CdSegmento'] = $info[$prefix.'segmento_mercado'];
        @$campos_sql['Ativo_Inativo_ExCliente'] = $info[$prefix.'status'];
        @$campos_sql['RestricaoSN'] = $info[$prefix.'restricao'];
        @$campos_sql['DiaPgto'] = null;
        @$campos_sql['ContaContabil'] = null;
        @$campos_sql['ClienteBrasil'] =null;
        @$campos_sql['DtAtivacaoFim'] = null;
        @$campos_sql['DtFundacao'] = $info[$prefix.'data_fundacao'];
        @$campos_sql['DtNascimento'] = null;
        @$campos_sql['CapitalSocial'] = 0;
        @$campos_sql['Redespacho'] = null;
        @$campos_sql['E_Email1'] = $info[$prefix.'email'];
        @$campos_sql['PeDesconto'] = $info[$prefix.'desconto'];
        @$campos_sql['Suframa'] = $info[$prefix.'suframa'];
        @$campos_sql['DtValidadeSuframa'] = $info[$prefix.'suframa_validade'];
        @$campos_sql['Site'] = $info[$prefix.'site'];
        @$campos_sql['OrgaoEmissor'] = null;

        @$campos_sql['Contato1'] = $info[$prefix.'contato_cobranca'];
        @$campos_sql['Funcao1'] = null;
        @$campos_sql['Email1'] = $info[$prefix.'contato_compras_email'];
        @$campos_sql['Funcao3'] = $info[$prefix.'contato_cobranca_funcao'];
        @$campos_sql['Email3'] = $info[$prefix.'contato_cobranca_email'];
        @$campos_sql['Contato3'] = $info[$prefix.'contato_cobranca'];
        @$campos_sql['Contato2'] = $info[$prefix.'endereco_entrega_contato'];
        @$campos_sql['Funcao2'] = null;
        @$campos_sql['Email2'] = $info[$prefix.'endereco_entrega_contato_email'];
        @$campos_sql['E_Cnpj'] = $info[$prefix.'endereco_entrega_cnpj'];
        @$campos_sql['E_Ie'] = $info[$prefix.'endereco_entrega_ie'];


        @$campos_sql['F_Cep'] = $info[$prefix.'endereco_cep'];
        @$campos_sql['F_Endereco'] = $info[$prefix.'endereco_endereco'];
        @$campos_sql['F_Complemento'] = $info[$prefix.'endereco_complemento'];
        @$campos_sql['F_Bairro'] = $info[$prefix.'endereco_bairro'];
        @$campos_sql['F_Cidade'] = $info[$prefix.'endereco_cidade'];
        @$campos_sql['F_Estado'] = $info[$prefix.'endereco_estado'];
        @$campos_sql['F_Numero'] = $info[$prefix.'endereco_numero'];
        @$campos_sql['C_Cep'] = $info[$prefix.'endereco_cobranca_cep'];
        @$campos_sql['C_Endereco'] = $info[$prefix.'endereco_cobranca_endereco'];
        @$campos_sql['C_Complemento'] = $info[$prefix.'endereco_cobranca_complemento'];
        @$campos_sql['C_Bairro'] = $info[$prefix.'endereco_cobranca_bairro'];
        @$campos_sql['C_Cidade'] = $info[$prefix.'endereco_cobranca_cidade'];
        @$campos_sql['C_Estado'] = $info[$prefix.'endereco_cobranca_estado'];
        @$campos_sql['C_Numero'] = $info[$prefix.'endereco_cobranca_numero'];
        @$campos_sql['E_Cep'] = $info[$prefix.'endereco_entrega_cep'];
        @$campos_sql['E_Endereco'] = $info[$prefix.'endereco_entrega_endereco'];
        @$campos_sql['E_Complemento'] = $info[$prefix.'endereco_entrega_complemento'];
        @$campos_sql['E_Bairro'] = $info[$prefix.'endereco_entrega_bairro'];
        @$campos_sql['E_Cidade'] = $info[$prefix.'endereco_entrega_cidade'];
        @$campos_sql['E_Estado'] = $info[$prefix.'endereco_entrega_estado'];
        @$campos_sql['E_Numero'] = $info[$prefix.'endereco_entrega_numero'];




        @$campos_sql['F_Ddd1'] = substr($info[$prefix.'contato_compras_fone'], 1, 2);
        @$campos_sql['F_Telefone1'] = trim(substr($info[$prefix.'contato_compras_fone'], 5, strlen($info[$prefix.'contato_compras_fone'])));
        @$campos_sql['F_Contato1'] = $info[$prefix.'contato_compras'];
        @$campos_sql['F_Email1'] = $info[$prefix.'contato_compras_email'];



        @$campos_sql['E_Ddd1'] = substr($info[$prefix.'endereco_entrega_contato_telefone'], 1, 2);
        @$campos_sql['E_Telefone1'] = trim(substr($info[$prefix.'endereco_entrega_contato_telefone'], 5, strlen($info[$prefix.'endereco_entrega_contato_telefone'])));
        @$campos_sql['E_Contato1'] = $info[$prefix.'endereco_entrega_contato'];
        @$campos_sql['E_Email1'] = $info[$prefix.'endereco_entrega_contato_email'];

        @$campos_sql['Ddd1'] = substr($info[$prefix.'contato_cobranca_fone'], 1, 2);
        @$campos_sql['Telefone1'] = trim(substr($info[$prefix.'contato_cobranca_fone'], 5, strlen($info[$prefix.'contato_cobranca_fone'])));
        @$campos_sql['Email1'] =  $info[$prefix.'contato_cobranca_email'];
        @$campos_sql['Funcao1'] =  $info[$prefix.'contato_cobranca_funcao'];

        @$campos_sql['C_Ddd1'] = substr($info[$prefix.'endereco_cobranca_contato_telefone'], 1, 2);
        @$campos_sql['C_Telefone1'] = trim(substr($info[$prefix.'endereco_cobranca_contato_telefone'], 5, strlen($info[$prefix.'endereco_cobranca_contato_telefone'])));
        @$campos_sql['C_Contato1'] = $info[$prefix.'endereco_cobranca_contato'];
        @$campos_sql['C_Email1'] = $info[$prefix.'endereco_cobranca_contato_email'];

        @$campos_sql['RestricaoSN'] = $_POST['campo_restricao'];

        @$campos_sql['DddFax'] = substr($info[$prefix.'fax'], 1, 2);
        @$campos_sql['Fax'] = trim(substr($info[$prefix.'fax'], 5, strlen($info[$prefix.'fax'])));
        // var_dump($campos_sql['Ddd1']);
        // var_dump($campos_sql['Telefone1']);
        // exit;

        @$campos_sql['ObsRestricao'] = $info[$prefix.'restricao_texto'];
        @$campos_sql['Observacao'] = $info[$prefix.'observacoes'];


        $error_message = "";
        $error = 0;
        if(@is_array($obrigatorios)){
            foreach($obrigatorios as $ob){
                if(!isset($info[$prefix.$ob]) or $info[$prefix.$ob] == ""){
                    $error++;
                    $error_message .= "$ob é requerido <br />";
                }
            }
        }

        if($error > 0){
            echo $error_message;
            exit;
        }

        // $info[$prefix.'tel1'] = trim(str_replace(' ', '', str_replace('(', '', str_replace('-', '', str_replace(')', '', $info[$prefix.'tel1'])))));
        // $info[$prefix.'fax'] = trim(str_replace(' ', '', str_replace('(', '', str_replace('-', '', str_replace(')', '', $info[$prefix.'fax'])))));
        // $info[$prefix.'contato_compras_fone'] = trim(str_replace(' ', '', str_replace('(', '', str_replace('-', '', str_replace(')', '', $info[$prefix.'contato_compras_fone'])))));
        // $info[$prefix.'contato_cobranca_fone'] = trim(str_replace(' ', '', str_replace('(', '', str_replace('-', '', str_replace(')', '', $info[$prefix.'contato_cobranca_fone'])))));
        // $info[$prefix.'endereco_cobranca_contato_telefone'] = trim(str_replace(' ', '', str_replace('(', '', str_replace('-', '', str_replace(')', '', $info[$prefix.'endereco_cobranca_contato_telefone'])))));
        // $info[$prefix.'endereco_entrega_contato_telefone'] = trim(str_replace(' ', '', str_replace('(', '', str_replace('-', '', str_replace(')', '', $info[$prefix.'endereco_entrega_contato_telefone'])))));
        //



        $info[$prefix.'cpfcnpj'] = trim(str_replace('.', '', str_replace('-', '', str_replace('/', '', $info[$prefix.'cpfcnpj']))));
        // $info[$prefix.'endereco_cep'] = str_replace('-', '', $info[$prefix.'endereco_cep']);

        $cpfcnpj = $info[$prefix.'cpfcnpj'];
        $Valida = new Valida();

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




    $sql = "UPDATE BusinessCadCliente SET
    FsCliente = :FsCliente,
    RzCliente = :RzCliente,
    FlTipo = :FlTipo,
    CDNATUREZA = :CDNATUREZA,
    CdSegmento = :CdSegmento,
    Ie_Rg = :Ie_Rg,
    C_Complemento = :C_Complemento,
    E_Numero = :E_Numero,
    E_Complemento = :E_Complemento,
    F_Numero = :F_Numero,
    F_Complemento = :F_Complemento,
    C_Numero = :C_Numero,
    Suframa = :Suframa,
    DtValidadeSuframa = :DtValidadeSuframa,
    CdGrupo = :CdGrupo,
    CdRegiao = :CdRegiao,
    Aviso = :Aviso,
    E_Ddd1 = :E_Ddd1,
    E_Telefone1 = :E_Telefone1,
    E_Email1 = :E_Email1,
    E_Cidade = :E_Cidade,
    E_Estado = :E_Estado,
    E_Cep = :E_Cep,
    E_Cnpj = :E_Cnpj,
    E_Ie = :E_Ie,
    E_Contato1 = :E_Contato1,
    C_Ddd1 = :C_Ddd1,
    C_Telefone1 = :C_Telefone1,
    C_Email1 = :C_Email1,
    E_Endereco = :E_Endereco,
    E_Bairro = :E_Bairro,
    E_CdMunicipio = :E_CdMunicipio,
    C_Bairro = :C_Bairro,
    C_CdMunicipio = :C_CdMunicipio,
    C_Cidade = :C_Cidade,
    C_Estado = :C_Estado,
    C_Cep = :C_Cep,
    C_Contato1 = :C_Contato1,
    F_Cep = :F_Cep,
    F_Contato1 = :F_Contato1,
    F_Ddd1 = :F_Ddd1,
    F_Telefone1 = :F_Telefone1,
    F_Email1 = :F_Email1,
    C_Endereco = :C_Endereco,
    Site = :Site,
    F_Endereco = :F_Endereco,
    F_Bairro = :F_Bairro,
    F_CdMunicipio = :F_CdMunicipio,
    F_Cidade = :F_Cidade,
    F_Estado = :F_Estado,
    Funcao3 = :Funcao3,
    Ddd3 = :Ddd3,
    Telefone3 = :Telefone3,
    Email3 = :Email3,
    DddFax = :DddFax,
    Fax = :Fax,
    Contato2 = :Contato2,
    Funcao2 = :Funcao2,
    Ddd2 = :Ddd2,
    Telefone2 = :Telefone2,
    Email2 = :Email2,
    Contato3 = :Contato3,
    Contato1 = :Contato1,
    Funcao1 = :Funcao1,
    Ddd1 = :Ddd1,
    Telefone1 = :Telefone1,
    Email1 = :Email1,
    ObsRestricao = :ObsRestricao,
    Observacao = :Observacao,
    RestricaoSN = :RestricaoSN
WHERE CdRepresentante = :CdRepresentante AND
Cnpj_Cnpf = :Cnpj_Cnpf";
    try {

    $sql = $con->prepare($sql);
    $sql->bindParam('Ie_Rg',$campos_sql['Ie_Rg']);
    $sql->bindParam('FsCliente',$campos_sql['FsCliente']);
    $sql->bindParam('RzCliente',$campos_sql['RzCliente']);
    $sql->bindParam('FlTipo',$campos_sql['FlTipo']);
    $sql->bindParam('CDNATUREZA',$campos_sql['CDNATUREZA']);
    $sql->bindParam('CdSegmento',$campos_sql['CdSegmento']);
    $sql->bindParam('C_Complemento',$campos_sql['C_Complemento']);
    $sql->bindParam('E_Numero',$campos_sql['E_Numero']);
    $sql->bindParam('E_Complemento',$campos_sql['E_Complemento']);
    $sql->bindParam('F_Numero',$campos_sql['F_Numero']);
    $sql->bindParam('F_Complemento',$campos_sql['F_Complemento']);
    $sql->bindParam('C_Numero',$campos_sql['C_Numero']);
    $sql->bindParam('Suframa',$campos_sql['Suframa']);
    $sql->bindParam('DtValidadeSuframa',$campos_sql['DtValidadeSuframa']);
    $sql->bindParam('CdGrupo',$campos_sql['CdGrupo']);


    $sql->bindParam('CdRegiao',$campos_sql['CdRegiao']);
    $sql->bindParam('Aviso',$campos_sql['Aviso']);
    $sql->bindParam('E_Ddd1',$campos_sql['E_Ddd1']);
    $sql->bindParam('E_Telefone1',$campos_sql['E_Telefone1']);
    $sql->bindParam('E_Email1',$campos_sql['E_Email1']);
    $sql->bindParam('E_Cidade',$campos_sql['E_Cidade']);
    $sql->bindParam('E_Estado',$campos_sql['E_Estado']);
    $sql->bindParam('E_Cep',$campos_sql['E_Cep']);
    $sql->bindParam('E_Cnpj',$campos_sql['E_Cnpj']);
    $sql->bindParam('E_Ie',$campos_sql['E_Ie']);
    $sql->bindParam('E_Contato1',$campos_sql['E_Contato1']);
    $sql->bindParam('C_Ddd1',$campos_sql['C_Ddd1']);
    $sql->bindParam('C_Telefone1',$campos_sql['C_Telefone1']);
    $sql->bindParam('C_Email1',$campos_sql['C_Email1']);
    $sql->bindParam('E_Endereco',$campos_sql['E_Endereco']);
    $sql->bindParam('E_Bairro',$campos_sql['E_Bairro']);
    $sql->bindParam('E_CdMunicipio',$campos_sql['E_CdMunicipio']);
    $sql->bindParam('C_Bairro',$campos_sql['C_Bairro']);
    $sql->bindParam('C_CdMunicipio',$campos_sql['C_CdMunicipio']);
    $sql->bindParam('C_Cidade',$campos_sql['C_Cidade']);
    $sql->bindParam('C_Estado',$campos_sql['C_Estado']);
    $sql->bindParam('C_Cep',$campos_sql['C_Cep']);
    $sql->bindParam('C_Contato1',$campos_sql['C_Contato1']);
    $sql->bindParam('F_Cep',$campos_sql['F_Cep']);
    $sql->bindParam('F_Contato1',$campos_sql['F_Contato1']);
    $sql->bindParam('F_Ddd1',$campos_sql['F_Ddd1']);
    $sql->bindParam('F_Telefone1',$campos_sql['F_Telefone1']);
    $sql->bindParam('F_Email1',$campos_sql['F_Email1']);
    $sql->bindParam('C_Endereco',$campos_sql['C_Endereco']);
    $sql->bindParam('Site',$campos_sql['Site']);
    $sql->bindParam('F_Endereco',$campos_sql['F_Endereco']);
    $sql->bindParam('F_Bairro',$campos_sql['F_Bairro']);
    $sql->bindParam('F_CdMunicipio',$campos_sql['F_CdMunicipio']);
    $sql->bindParam('F_Cidade',$campos_sql['F_Cidade']);
    $sql->bindParam('F_Estado',$campos_sql['F_Estado']);
    $sql->bindParam('Funcao3',$campos_sql['Funcao3']);
    $sql->bindParam('Ddd3',$campos_sql['Ddd3']);
    $sql->bindParam('Telefone3',$campos_sql['Telefone3']);
    $sql->bindParam('Email3',$campos_sql['Email3']);
    $sql->bindParam('DddFax',$campos_sql['DddFax']);
    $sql->bindParam('Fax',$campos_sql['Fax']);
    $sql->bindParam('Contato2',$campos_sql['Contato2']);
    $sql->bindParam('Funcao2',$campos_sql['Funcao2']);
    $sql->bindParam('Ddd2',$campos_sql['Ddd2']);
    $sql->bindParam('Telefone2',$campos_sql['Telefone2']);
    $sql->bindParam('Email2',$campos_sql['Email2']);
    $sql->bindParam('Contato3',$campos_sql['Contato3']);
    $sql->bindParam('Contato1',$campos_sql['Contato1']);
    $sql->bindParam('Funcao1',$campos_sql['Funcao1']);
    $sql->bindParam('Ddd1',$campos_sql['Ddd1']);
    $sql->bindParam('Telefone1',$campos_sql['Telefone1']);
    $sql->bindParam('Email1',$campos_sql['Email1']);
    $sql->bindParam('ObsRestricao',$campos_sql['ObsRestricao']);
    $sql->bindParam('Observacao',$campos_sql['Observacao']);
    $sql->bindParam('RestricaoSN',$campos_sql['RestricaoSN']);


    $sql->bindParam('Cnpj_Cnpf',$campos_sql['Cnpj_Cnpf']);
    $sql->bindParam('CdRepresentante',$campos_sql['CdRepresentante_old']);


    $sql->execute();

    }
    catch(Exception $e){
        $error_message = $e->getMessage();
        echo $error_message;
        exit;
    }
    catch(PDOException $ex){
        $error_message = $ex->getMessage();
        echo $error_message;
        exit;
    }

    @$empresas = $_POST['empresas'];
    $sql = "DELETE FROM BusinessCadClienteLC WHERE

    CdRepresentante = :CdRepresentante AND Cnpj_Cnpf = :Cnpj_Cnpf";
    try {
        $sql = $con->prepare($sql);
        $sql->bindParam('CdRepresentante', $representante_id);
        $sql->bindParam('Cnpj_Cnpf', $campos_sql['Cnpj_Cnpf']);

        @$sql->execute();
    }
    catch(Exception $e){
        $error_message = $e->getMessage();
        // echo $error_message;
        //exit;
    }
    catch(PDOException $ex){
        $error_message = $ex->getMessage();
        // echo $error_message;
        //exit;
    }
    if(@is_array($empresas)){
        foreach($empresas as $ep){
            $sql = "INSERT INTO BusinessCadClienteLC
            (CdRepresentante, CdEmpresa, Cnpj_Cnpf, LimiteCredito, VlUltCompra, FlEnvRecRepre)
            VALUES
            (:CdRepresentante, :CdEmpresa, :Cnpj_Cnpf, 0, 0, 'S')";

            try {


                $sql = $con->prepare($sql);
                $sql->bindParam('CdRepresentante', $representante_id);
                $sql->bindParam('CdEmpresa', $ep);
                $sql->bindParam('Cnpj_Cnpf', $campos_sql['Cnpj_Cnpf']);

                @$sql->execute();
            }
            catch(Exception $e){
                $error_message = $e->getMessage();
                // echo $error_message;
                //exit;
            }
            catch(PDOException $ex){
                $error_message = $ex->getMessage();
                // echo $error_message;
                //exit;
            }
        }
    }


    $sql = "DELETE FROM BusinessCadClienteSocio WHERE
    CdRepresentante = :CdRepresentante AND
    Cnpj_Cnpf = :Cnpj_Cnpf";
    try {
        $sql = $con->prepare($sql);
        $sql->bindParam('CdRepresentante', $campos_sql['CdRepresentante_old']);
        $sql->bindParam('Cnpj_Cnpf', $campos_sql['Cnpj_Cnpf']);
        $sql->execute();
    }
    catch(Exception $e){
        $error_message = $e->getMessage();
        echo $error_message;
        exit;
    }
    catch(PDOException $ex){
        $error_message = $ex->getMessage();
        echo $error_message;
        exit;
    }

    if(@is_array($socios)){
        foreach($socios as $sc){
            $sql = "INSERT INTO BusinessCadClienteSocio
            (
                CdRepresentante,
                Cnpj_Cnpf,
                CdSequencia,
                NmSocio,
                DtNascimento,
                FlEstCivil,
                QtdeFilhos,
                BebidaPreferida,
                TimeFutebol,
                EsportePratica
            )
            VALUES (
                :CdRepresentante,
                :Cnpj_Cnpf,
                :CdSequencia,
                :NmSocio,
                :DtNascimento,
                :FlEstCivil,
                :QtdeFilhos,
                :BebidaPreferida,
                :TimeFutebol,
                :EsportePratica
            )
            ";
            try {
                $sql = $con->prepare($sql);
                $sql->bindParam('CdRepresentante', $campos_sql['CdRepresentante_old']);
                $sql->bindParam('Cnpj_Cnpf', $campos_sql['Cnpj_Cnpf']);
                $sql->bindParam('CdSequencia', $sc['CdSequencia']);
                $sql->bindParam('NmSocio', $sc['NmSocio']);
                $sql->bindParam('DtNascimento', $sc['DtNascimento']);
                $sql->bindParam('FlEstCivil', $sc['FlEstCivil']);
                $sql->bindParam('QtdeFilhos', $sc['QtdeFilhos']);
                $sql->bindParam('BebidaPreferida', $sc['BebidaPreferida']);
                $sql->bindParam('TimeFutebol', $sc['TimeFutebol']);
                $sql->bindParam('EsportePratica', $sc['EsportePratica']);
                $sql->execute();
            }
            catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
            catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            }
        }
    }



    $sql = "DELETE FROM BusinessCadClienteGerSet WHERE
    CdRepresentante = :CdRepresentante AND
    Cnpj_Cnpf = :Cnpj_Cnpf";
    try {
        $sql = $con->prepare($sql);
        $sql->bindParam('CdRepresentante', $campos_sql['CdRepresentante_old']);
        $sql->bindParam('Cnpj_Cnpf', $campos_sql['Cnpj_Cnpf']);
        $sql->execute();
    }
    catch(Exception $e){
        $error_message = $e->getMessage();
        echo $error_message;
        exit;
    }
    catch(PDOException $ex){
        $error_message = $ex->getMessage();
        echo $error_message;
        exit;
    }


    if(@is_array($gerentes)){
        foreach($gerentes as $sc){
            $sql = "INSERT INTO BusinessCadClienteGerSet
            (
                CdRepresentante,
                Cnpj_Cnpf,
                CdSequencia,
                NmGerente,
                DtNascimento,
                FlEstCivil,
                QtdeFilhos,
                BebidaPreferida,
                TimeFutebol,
                EsportePratica
            )
            VALUES (
                :CdRepresentante,
                :Cnpj_Cnpf,
                :CdSequencia,
                :NmGerente,
                :DtNascimento,
                :FlEstCivil,
                :QtdeFilhos,
                :BebidaPreferida,
                :TimeFutebol,
                :EsportePratica
            )
            ";
            try {
                $sql = $con->prepare($sql);
                $sql->bindParam('CdRepresentante', $campos_sql['CdRepresentante_old']);
                $sql->bindParam('Cnpj_Cnpf', $campos_sql['Cnpj_Cnpf']);
                $sql->bindParam('CdSequencia', $sc['CdSequencia']);
                $sql->bindParam('NmGerente', $sc['NmGerente']);
                $sql->bindParam('DtNascimento', $sc['DtNascimento']);
                $sql->bindParam('FlEstCivil', $sc['FlEstCivil']);
                $sql->bindParam('QtdeFilhos', $sc['QtdeFilhos']);
                $sql->bindParam('BebidaPreferida', $sc['BebidaPreferida']);
                $sql->bindParam('TimeFutebol', $sc['TimeFutebol']);
                $sql->bindParam('EsportePratica', $sc['EsportePratica']);
                $sql->execute();
            }
            catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
            catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            }
        }
    }

    echo 0;
}
exit;
