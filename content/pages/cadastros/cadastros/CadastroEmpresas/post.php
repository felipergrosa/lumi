<?php
header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__.'/../../../../../dist/php/general.inc.php';
ini_set('display_errors', 'on');
@session_start();
$prefix = "cadastro_empresas_edit_form_";
if($_POST['action'] == 'GetUserList'){
    $sql = "SELECT
    CdEmpresa as id,
    FsEmpresa as nome_fantasia,
    RzEmpresa as razao_social

    FROM BusinessCadEmpresa";
    $sql = $con_sql_server->prepare($sql);
    $sql->execute();
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($row);
}
if($_POST['action'] == 'GetUserData'){
    $id = (int) $_POST['id'];
    // $sql = "SELECT * FROM empresas WHERE id = :id";
    $sql = "SELECT *,
    CdEmpresa as id,
    FsEmpresa as nome_fantasia,
    FsEmpresa as nome,
    RzEmpresa as razao_social

    FROM BusinessCadEmpresa WHERE
    CdEmpresa = :id";
    $sql = $con_sql_server->prepare($sql);
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
    $sql = "UPDATE empresas SET nome = :nome, email = :email WHERE id = :id";
    $sql = $con->prepare($sql);
    $sql->bindParam('nome', $nome);
    $sql->bindParam('email', $email);
    $sql->bindParam('id', $id);
    $sql->execute();
}
if($_POST['action'] == 'novo'){
    $obrigatorios = array(
        'nome',
        'email1'
    );
    $dados = $_POST['dados'];
    foreach ($dados as $dd) {
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
    $info[$prefix.'endereco_cep'] = str_replace('-', '', $info[$prefix.'endereco_cep']);
    try {
        $sql = "SELECT * FROM empresas WHERE cpfcnpj = :cpfcnpj";
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



    $sql = "INSERT INTO empresas
    (nome, cpfcnpj, email1, endereco, endereco_numero, endereco_bairro, endereco_cidade,
    endereco_estado,endereco_cep,observacoes)  VALUES (:nome, :cpfcnpj, :email1, :endereco,
    :endereco_numero, :endereco_bairro, :endereco_cidade, :endereco_estado, :endereco_cep,
    :observacoes)";
    try {
    $sql = $con->prepare($sql);
    $sql->bindParam('nome', $info[$prefix.'nome']);
    $sql->bindParam('email1', $info[$prefix.'email1']);
    $sql->bindParam('endereco', $info[$prefix.'endereco']);
    $sql->bindParam('endereco_numero', $info[$prefix.'endereco_numero']);
    $sql->bindParam('endereco_bairro', $info[$prefix.'endereco_bairro']);
    $sql->bindParam('endereco_cidade', $info[$prefix.'endereco_cidade']);
    $sql->bindParam('endereco_estado', $info[$prefix.'endereco_estado']);
    $sql->bindParam('endereco_cep', $info[$prefix.'endereco_cep']);
    $sql->bindParam('observacoes', $info[$prefix.'observacoes']);
    $sql->bindParam('cpfcnpj', $info[$prefix.'cpfcnpj']);
    $sql->execute();

    }
    catch(PDOException $e){
        $error_message = $e->getMessage();
        echo $error_message;
        exit;
    }

    echo 0;

}
if($_POST['action'] == 'editar'){
    $obrigatorios = array(
        'nome',
        'email1'
    );
    $dados = $_POST['dados'];
    foreach ($dados as $dd) {
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
    $info[$prefix.'endereco_cep'] = str_replace('-', '', $info[$prefix.'endereco_cep']);
    try {
        $sql = "SELECT * FROM empresas WHERE cpfcnpj = :cpfcnpj && id != :id";
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



    $sql = "UPDATE empresas SET
    nome = :nome,
    cpfcnpj = :cpfcnpj,
    email1 = :email1,
    endereco = :endereco,
    endereco_numero = :endereco_numero,
    endereco_bairro = :endereco_bairro,
    endereco_cidade = :endereco_cidade,
    endereco_estado = :endereco_estado,
    endereco_cep = :endereco_cep,
    observacoes = :observacoes
    WHERE id = :id";
    try {
    $sql = $con->prepare($sql);
    $sql->bindParam('id', $info[$prefix.'id']);
    $sql->bindParam('nome', $info[$prefix.'nome']);
    $sql->bindParam('email1', $info[$prefix.'email1']);
    $sql->bindParam('endereco', $info[$prefix.'endereco']);
    $sql->bindParam('endereco_numero', $info[$prefix.'endereco_numero']);
    $sql->bindParam('endereco_bairro', $info[$prefix.'endereco_bairro']);
    $sql->bindParam('endereco_cidade', $info[$prefix.'endereco_cidade']);
    $sql->bindParam('endereco_estado', $info[$prefix.'endereco_estado']);
    $sql->bindParam('endereco_cep', $info[$prefix.'endereco_cep']);
    $sql->bindParam('observacoes', $info[$prefix.'observacoes']);
    $sql->bindParam('cpfcnpj', $info[$prefix.'cpfcnpj']);
    $sql->execute();

    }
    catch(PDOException $e){
        $error_message = $e->getMessage();
        echo $error_message;
        exit;
    }

    echo 0;
}
exit;
