<?php
header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__.'/../../../../../dist/php/general.inc.php';
ini_set('display_errors', 'on');
@session_start();
if(@$_POST['action'] == 'permissao_front'){
    var_dump($_POST);
    $CdRepresentante = $_POST['CdRepresentante'];
    $check = $_POST['checked'];
    $tela = $_POST['tela'];

    if($check == "true"){

        $sql = "INSERT INTO BusinessCadPermiAcessos (usuario, tela, status)
        VALUES (:CdRepresentante, :tela, 1)";
        try {
            $sql = $con_sql_server->prepare($sql);
            $sql->bindParam('CdRepresentante', $CdRepresentante);
            $sql->bindParam('tela', $tela);
            $sql->execute();
        } catch(PDOException $ex){
            $error_message = $ex->getMessage();
            echo $error_message;
            exit;
        } catch(Exception $e){
            $error_message = $e->getMessage();
            echo $error_message;
            exit;
        }
    }

    else {
        $sql = "DELETE FROM BusinessCadPermiAcessos
        WHERE usuario = :CdRepresentante AND tela = :tela";
        try {
            $sql = $con_sql_server->prepare($sql);
            $sql->bindParam('CdRepresentante', $CdRepresentante);
            $sql->bindParam('tela', $tela);
            $sql->execute();

        } catch(PDOException $ex){
            $error_message = $ex->getMessage();
            echo $error_message;
            exit;
        } catch(Exception $e){
            $error_message = $e->getMessage();
            echo $error_message;
            exit;
        }
    }
    exit;
}
if(@$_POST['action'] == 'permissao'){
    // var_dump($_POST);
    $CdRepresentante = $_POST['CdRepresentante'];
    $valor = $_POST['valor'];
    $alvo = $_POST['alvo'];
    $check = $_POST['checked'];
    // var_dump($_POST);

    // exit;

    if($alvo == "cadastro_usuarios_edit_div_perm_condpgto"){
        $valor = explode("|", $valor);
        $CdCondPgto = $valor[0];
        $CdEmpresa = $valor[1];
        if($check == "true"){
            $sql = "INSERT INTO BusinessCadPermiCondPgto (CdRepresentante, CdEmpresa, CdCondPgto)
            VALUES
            (:CdRepresentante, :CdEmpresa, :CdCondPgto)";
            try {
                $sql = $con_sql_server->prepare($sql);
                $sql->bindParam('CdRepresentante', $CdRepresentante);
                $sql->bindParam('CdEmpresa', $CdEmpresa);
                $sql->bindParam('CdCondPgto', $CdCondPgto);
                $sql->execute();
            } catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            } catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
        }
        else {
            $sql = "DELETE FROM BusinessCadPermiCondPgto
            WHERE CdRepresentante = :CdRepresentante AND CdEmpresa = :CdEmpresa AND
            CdCondPgto = :CdCondPgto";
            try {
                $sql = $con_sql_server->prepare($sql);
                $sql->bindParam('CdRepresentante', $CdRepresentante);
                $sql->bindParam('CdEmpresa', $CdEmpresa);
                $sql->bindParam('CdCondPgto', $CdCondPgto);
                $sql->execute();
            } catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            } catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
        }
        exit;
    }


    if($alvo == "cadastro_usuarios_edit_div_perm_municipios"){
        if($check == "true"){
            $sql = "INSERT INTO BusinessCadPermiMunicipio (CdRepresentante, Cdmunicipio)
            VALUES
            (:CdRepresentante, :Cdmunicipio)";
            try {
                $sql = $con_sql_server->prepare($sql);
                $sql->bindParam('CdRepresentante', $CdRepresentante);
                $sql->bindParam('Cdmunicipio', $valor);
                $sql->execute();
            } catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            } catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
        }
        else {
            $sql = "DELETE FROM BusinessCadPermiMunicipio
            WHERE CdRepresentante = :CdRepresentante AND Cdmunicipio = :Cdmunicipio";
            try {
                $sql = $con_sql_server->prepare($sql);
                $sql->bindParam('CdRepresentante', $CdRepresentante);
                $sql->bindParam('Cdmunicipio', $valor);
                $sql->execute();
            } catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            } catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
        }
        exit;
    }



    if($alvo == "cadastro_usuarios_edit_div_perm_regiao"){
        if($check == "true"){
            $sql = "INSERT INTO BusinessPermiRegiao (CdRepresentante, CdRegiao)
            VALUES
            (:CdRepresentante, :CdRegiao)";
            try {
                $sql = $con_sql_server->prepare($sql);
                $sql->bindParam('CdRepresentante', $CdRepresentante);
                $sql->bindParam('CdRegiao', $valor);
                $sql->execute();
            } catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            } catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
        }
        else {
            $sql = "DELETE FROM BusinessPermiRegiao
            WHERE CdRepresentante = :CdRepresentante AND CdRegiao = :CdRegiao";
            try {
                $sql = $con_sql_server->prepare($sql);
                $sql->bindParam('CdRepresentante', $CdRepresentante);
                $sql->bindParam('CdRegiao', $valor);
                $sql->execute();
            } catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            } catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
        }
        exit;
    }


    if($alvo == "cadastro_usuarios_edit_div_perm_nat_op"){
        if($check == "true"){
            $sql = "INSERT INTO BusinessCadPerminatureza (CdRepresentante, CdNatureza)
            VALUES
            (:CdRepresentante, :CdNatureza)";
            try {
                $sql = $con_sql_server->prepare($sql);
                $sql->bindParam('CdRepresentante', $CdRepresentante);
                $sql->bindParam('CdNatureza', $valor);
                $sql->execute();
            } catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            } catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
        }
        else {
            $sql = "DELETE FROM BusinessCadPerminatureza
            WHERE CdRepresentante = :CdRepresentante AND CdNatureza = :CdNatureza";
            try {
                $sql = $con_sql_server->prepare($sql);
                $sql->bindParam('CdRepresentante', $CdRepresentante);
                $sql->bindParam('CdNatureza', $valor);
                $sql->execute();
            } catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            } catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
        }
        exit;
    }

    if($alvo == "cadastro_usuarios_edit_div_perm_prio"){
        $valor = explode("|", $valor);
        $coluna = $valor[1];
        $CdEmpresa = $valor[0];

        if($check == "true"){
            $sql = "SELECT * FROM BusinessCadPermiPrioridade WHERE CdEmpresa = :CdEmpresa
            AND cdRepresentante = :cdRepresentante";
            try {
                $sql = $con_sql_server->prepare($sql);
                $sql->bindParam('cdRepresentante', $CdRepresentante);
                $sql->bindParam('CdEmpresa', $CdEmpresa);
                $sql->execute();
            } catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            } catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }

            if($sql->rowCount() != 0){
                $sql = "UPDATE BusinessCadPermiPrioridade SET ".$coluna." = 'S' WHERE
                CdEmpresa = :CdEmpresa
                AND cdRepresentante = :cdRepresentante";
                try {
                    $sql = $con_sql_server->prepare($sql);
                    $sql->bindParam('cdRepresentante', $CdRepresentante);
                    $sql->bindParam('CdEmpresa', $CdEmpresa);
                    $sql->execute();
                } catch(PDOException $ex){
                    $error_message = $ex->getMessage();
                    echo $error_message;
                    exit;
                } catch(Exception $e){
                    $error_message = $e->getMessage();
                    echo $error_message;
                    exit;
                }
            }

        }
        else {
            $sql = "UPDATE BusinessCadPermiPrioridade SET ".$coluna." = 'N' WHERE
            CdEmpresa = :CdEmpresa
            AND cdRepresentante = :cdRepresentante";
            try {
                $sql = $con_sql_server->prepare($sql);
                $sql->bindParam('cdRepresentante', $CdRepresentante);
                $sql->bindParam('CdEmpresa', $CdEmpresa);
                $sql->execute();
            } catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            } catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
        }
        exit;
    }
    if($alvo == "cadastro_usuarios_edit_div_perm_seg_merc"){
        if($check == "true"){
            $sql = "INSERT INTO BusinessCadPermiSegMercado (CdRepresentante, CdSegmento)
            VALUES
            (:CdRepresentante, :CdSegmento)";
            try {
                $sql = $con_sql_server->prepare($sql);
                $sql->bindParam('CdRepresentante', $CdRepresentante);
                $sql->bindParam('CdSegmento', $valor);
                $sql->execute();
            } catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            } catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
        }
        else {
            $sql = "DELETE FROM BusinessCadPermiSegMercado
            WHERE CdRepresentante = :CdRepresentante AND CdSegmento = :CdSegmento";
            try {
                $sql = $con_sql_server->prepare($sql);
                $sql->bindParam('CdRepresentante', $CdRepresentante);
                $sql->bindParam('CdSegmento', $valor);
                $sql->execute();
            } catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            } catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
        }
        exit;
    }

    if($alvo == "cadastro_usuarios_edit_div_perm_tabela"){
        $valor = explode("|", $valor);
        $CdTabela = $valor[0];
        $CdEmpresa = $valor[1];

        if($check == "true"){
            $sql = "INSERT INTO BusinessCadPermiTabela (CdRepresentante, CdEmpresa, CdTabela)
            VALUES
            (:CdRepresentante, :CdEmpresa, :CdTabela)";
            try {
                $sql = $con_sql_server->prepare($sql);
                $sql->bindParam('CdRepresentante', $CdRepresentante);
                $sql->bindParam('CdEmpresa', $CdEmpresa);
                $sql->bindParam('CdTabela', $CdTabela);

                $sql->execute();
            } catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            } catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
        }
        else {
            $sql = "DELETE FROM BusinessCadPermiTabela
            WHERE CdRepresentante = :CdRepresentante AND CdEmpresa = :CdEmpresa
            AND CdTabela = :CdTabela";
            try {
                $sql = $con_sql_server->prepare($sql);
                $sql->bindParam('CdRepresentante', $CdRepresentante);
                $sql->bindParam('CdEmpresa', $CdEmpresa);
                $sql->bindParam('CdTabela', $CdTabela);
                $sql->execute();
            } catch(PDOException $ex){
                $error_message = $ex->getMessage();
                echo $error_message;
                exit;
            } catch(Exception $e){
                $error_message = $e->getMessage();
                echo $error_message;
                exit;
            }
        }
        exit;
    }

    exit;
}
if($_POST['action'] == 'GetUserList'){
    try {
    $sql = "SELECT * FROM BusinessCadRepresentante";
    $sql = $con_sql_server->query($sql);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $ex){
        $error_message = $ex->getMessage();
        echo $error_message;
        exit;
    } catch(Exception $e){
        $error_message = $e->getMessage();
        echo $error_message;
        exit;
    }
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
