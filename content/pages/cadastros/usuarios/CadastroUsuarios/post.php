<?php
header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__.'/../../../../../dist/php/general.inc.php';
ini_set('display_errors', 'on');
@session_start();
if($_POST['action'] == 'GetUserList'){
    $sql = "SELECT * FROM BusinessCadRepresentante";
    $sql = $con_sql_server->query($sql);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($row);
}
if($_POST['action'] == 'GetUserData'){
    $id = $_POST['id'];
    $sql = "SELECT * FROM BusinessCadRepresentante WHERE CdRepresentante = :id";
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
    $sql = "UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id";
    $sql = $con->prepare($sql);
    $sql->bindParam('nome', $nome);
    $sql->bindParam('email', $email);
    $sql->bindParam('id', $id);
    $sql->execute();
}
if($_POST['action'] == 'novo'){
    $obrigatorios = array(
        'login',
        'email',
        'pass'
    );
    $prefix = "cadastro_usuarios_edit_form_";
    $dados = $_POST['dados'];
    foreach ($dados as $dd) {
        $check = substr($dd['name'], 0, 4);
        $check2 = substr($dd['name'], 0, 8);
        $check3 = substr($dd['name'], 0, 9);
        if ($check == 'tela') {
            $acessos[$dd['value']] = $dd['value'];
        } elseif($check2 == 'especial'){
            $especiais[$dd['value']] = $dd['value'];
        }
        elseif($check == 'fila'){
            $filas[$dd['value']] = $dd['value'];
        }
        elseif($check3 == 'operadora'){
            $operadoras[$dd['value']] = $dd['value'];
        }
        else {
            $info[$dd['name']] = $dd['value'];
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

    if($info[$prefix.'pass'] == ""){
        echo "Senha não pode estar em branco";
        exit;
    }
    try {
        $sql = "SELECT * FROM usuarios WHERE login = :login";
        $sql = $con->prepare($sql);
        $sql->bindParam('login', $info[$prefix.'login']);
        $sql->execute();
    }
    catch(Exception $e){
        $error_message = $e->getMessage();
        echo $error_message;
        exit;
    }
    if($sql->rowCount() > 0){
        echo 'login ja existe, escolha outro.';
        exit;
    }


    $pass = sha1(md5(sha1(md5($info[$prefix.'pass']))));
    $sql = "INSERT INTO usuarios (nome, login, email, pass) VALUES (:nome, :login, :email, :pass)";
    $sql = $con->prepare($sql);
    $sql->bindParam('nome', $info[$prefix.'nome']);
    $sql->bindParam('login', $info[$prefix.'login']);
    $sql->bindParam('email', $info[$prefix.'email']);
    $sql->bindParam('pass', $pass);
    $sql->execute();

    $info[$prefix.'id'] = $con->lastInsertId();

    foreach($acessos as $as){
        $sql = "INSERT INTO usuarios_acessos (usuario, tela) VALUES (:id, :tela)";
        $sql = $con->prepare($sql);
        $sql->bindParam('id', $info[$prefix.'id']);
        $sql->bindParam('tela', $as);
        $sql->execute();

    }

    echo 0;

}
if($_POST['action'] == 'editar'){
    $obrigatorios = array(
        'login',
        'email'
    );
    $prefix = "cadastro_usuarios_edit_form_";
    $dados = $_POST['dados'];
    foreach ($dados as $dd) {
        $check = substr($dd['name'], 0, 4);
        $check2 = substr($dd['name'], 0, 8);
        $check3 = substr($dd['name'], 0, 9);
        if ($check == 'tela') {
            $acessos[$dd['value']] = $dd['value'];
        } elseif($check2 == 'especial'){
            $especiais[$dd['value']] = $dd['value'];
        }
        elseif($check == 'fila'){
            $filas[$dd['value']] = $dd['value'];
        }
        elseif($check3 == 'operadora'){
            $operadoras[$dd['value']] = $dd['value'];
        }
        else {
            $info[$dd['name']] = $dd['value'];
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

    try {
        $sql = "SELECT * FROM usuarios WHERE login = :login && id != :id";
        $sql = $con->prepare($sql);
        $sql->bindParam('login', $info[$prefix.'login']);
        $sql->bindParam('id', $info[$prefix.'id']);
        $sql->execute();
    }
    catch(Exception $e){
        $error_message = $e->getMessage();
        echo $error_message;
        exit;
    }
    if($sql->rowCount() > 0){
        echo 'login ja existe, escolha outro.';
        exit;
    }

    if($info[$prefix.'pass'] != ""){
        $pass = sha1(md5(sha1(md5($info[$prefix.'pass']))));
        $sql = "UPDATE usuarios SET nome = :nome, login = :login, email = :email, pass = :pass WHERE id = :id";
        $sql = $con->prepare($sql);
        $sql->bindParam('id', $info[$prefix.'id']);
        $sql->bindParam('nome', $info[$prefix.'nome']);
        $sql->bindParam('login', $info[$prefix.'login']);
        $sql->bindParam('email', $info[$prefix.'email']);
        $sql->bindParam('pass', $pass);
        $sql->execute();
    }
    else {
        $sql = "UPDATE usuarios SET nome = :nome, login = :login, email = :email WHERE id = :id";
        $sql = $con->prepare($sql);
        $sql->bindParam('id', $info[$prefix.'id']);
        $sql->bindParam('nome', $info[$prefix.'nome']);
        $sql->bindParam('login', $info[$prefix.'login']);
        $sql->bindParam('email', $info[$prefix.'email']);
        $sql->execute();
    }
    $sql = "DELETE FROM usuarios_acessos WHERE usuario = :id";
    $sql = $con->prepare($sql);
    $sql->bindParam('id', $info[$prefix.'id']);
    $sql->execute();

    foreach($acessos as $as){
        $sql = "INSERT INTO usuarios_acessos (usuario, tela) VALUES (:id, :tela)";
        $sql = $con->prepare($sql);
        $sql->bindParam('id', $info[$prefix.'id']);
        $sql->bindParam('tela', $as);
        $sql->execute();

    }

    echo 0;
}
exit;
