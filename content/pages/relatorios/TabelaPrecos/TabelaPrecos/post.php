<?php
header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__.'/../../../../../dist/php/general.inc.php';
ini_set('display_errors', 'on');
@session_start();
$prefix = "cadastro_produtos_edit_form_";
if(@$_POST['action'] == "BuscaPrecos"){
    // var_dump($_POST);

    $CdEmpresa = $_POST['CdEmpresa'];
    $CdTabela = $_POST['CdTabela'];


    $sql = "SELECT a.*, b.PeIpi, b.Unidade, b.CdBarra FROM BusinessCadTabPrecoItem a
    LEFT JOIN BusinessCadProduto b ON a.CdProduto=b.CdProduto AND a.CdEmpresa=b.CdEmpresa

    WHERE a.CdEmpresa = :CdEmpresa AND a.CdTabela = :CdTabela ";
    try {
        $sql = $con->prepare($sql);
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

    $cont = 0;
    while($row1 = $sql->fetch(PDO::FETCH_ASSOC)){
        $row[$cont] = $row1;
        $row[$cont]['Venda'] = "R$".number_format($row[$cont]['Venda'], 2, ',', '.' );
        $cont++;
    }
    // $row = $sql->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(utf8ize($row));
    exit;
}








if(@$_POST['action'] == 'AplicaDesconto'){
    $CdProduto = $_POST['CdProduto'];
    $check = $_POST['checked'];
    $CdEmpresa = $_POST['CdEmpresa'];
    // var_dump($_POST);
    // exit;


    if($check == "true"){
        $troca = "S";
    }
    else {
        $troca = "N";
    }



    $sql = "UPDATE BusinessCadProduto SET pdesc = :pdesc WHERE
    CdProduto = :CdProduto AND CdEmpresa = :CdEmpresa";
    try {
        $sql = $con_sql_server->prepare($sql);
        $sql->bindParam('pdesc', $troca);
        $sql->bindParam('CdProduto', $CdProduto);
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

    echo 0;
    exit;

}
if($_POST['action'] == 'GetProdList'){
    $empresa = $_POST['empresa'];
    $sql = "SELECT *,
    a.DsProduto as nome,
    a.CdProduto as id,
    b.FsEmpresa as empresa,
    a.CdEmpresa,
    0 as valor,
    a.pdesc
    FROM BusinessCadProduto a
    LEFT JOIN BusinessCadEmpresa b ON a.CdEmpresa=b.CdEmpresa

    WHERE a.CdEmpresa = :empresa";
    $sql = $con_sql_server->prepare($sql);
    $sql->bindParam('empresa', $empresa);
    $sql->execute();
    $foto_dir = __DIR__.'/docs/';
    $cont = 0;
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $saida[$cont] = $row;
        if($row['pdesc'] == "S"){
            $check = "checked='checked'";
        }
        else {
            $check = "";
        }

        $pode_desc = '<input type="checkbox" onclick="AplicaDesconto(\''.$row['CdProduto'].'\', \''.$row['CdEmpresa'].'\', this)" '.$check.' />';
        $saida[$cont]['pode_desc'] = $pode_desc;
        if(@file_exists($foto_dir.$row['CdEmpresa'].'-'.$row['CdProduto'].'.jpg')){
            $saida[$cont]['foto'] = '<i class="fa fa-eye"></i>';
        }
        else {
            $saida[$cont]['foto'] = '';

        }
        $cont++;
    }
    // $row = $sql->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(utf8ize($saida));
}
if($_POST['action'] == 'GetProdData'){
    $id = $_POST['id'];
    $empresa = $_POST['empresa'];
    // $sql = "SELECT * FROM empresas WHERE id = :id";
    $sql = "SELECT *,
    DsProduto as descricao,
    CdProduto as id,
    CdEmpresa as empresa


    FROM BusinessCadProduto WHERE
    CdEmpresa = :empresa AND CdProduto = :id";
    $sql = $con_sql_server->prepare($sql);
    $sql->bindParam('id', $id);
    $sql->bindParam('empresa', $empresa);

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

    $obrigatorios = array();
    $dados = json_decode($_POST['dados'], 1);
    foreach ($dados as $dd) {
        $info[$dd['name']] = $dd['value'];
    }

    if(@$_FILES['file']){
        $upload_dir = __DIR__.'/docs/';
        // if(!is_dir(__DIR__.'/docs/')){
        //     mkdir(__DIR__.'/docs/');
        // }
        $filename = $info[$prefix.'CdEmpresa'].'-'.$info[$prefix.'CdProduto'].'.jpg';
        try {
            $fn = $_FILES['file']['tmp_name'];
            $size = getimagesize($fn);
            $ratio = $size[0]/$size[1]; // width/height
            if( $ratio > 1) {
                $width = 500;
                $height = 500/$ratio;
            }
            else {
                $width = 500*$ratio;
                $height = 500;
            }

            // echo $width.'-'.$height;
            $src = imagecreatefromstring(file_get_contents($fn));
            $dst = imagecreatetruecolor($width,$height);
            imagecopyresampled($dst,$src,0,0,0,0,$width,$height,$size[0],$size[1]);
            imagedestroy($src);
            imagejpeg($dst,$upload_dir.$filename);
        } catch(Exception $e){
            echo $e->getMessage();
            exit;
        }
    }


    echo 0;
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

    try {
        // $sql = "UPDATE BusinessCadProduto SET
        // CdEmpresa = :CdEmpresa,
        // CdProduto = :CdProduto,
        // CdBarra = :CdBarra,
        // DsProduto = :DsProduto,
        // Unidade = :Unidade,
        // QtdeEmbalagem = :QtdeEmbalagem,
        // CustoReal = :CustoReal,
        // DtSincronizar = :DtSincronizar,
        // FlStatus = :FlStatus,
        // CDGRUPO = :CDGRUPO,
        // FlPadrao = :FlPadrao,
        // FlEspecial = :FlEspecial,
        // PeIcms = :PeIcms,
        // PeIpi = :PeIpi,
        // PeReducao = :PeReducao,
        // PeMargemLucro = :PeMargemLucro,
        // Unitario = :Unitario,
        // Medio = :Medio,
        // Saldo = :Saldo
        //
        // WHERE CdProduto = :OldCdProduto AND
        // CdEmpresa = :OldCdEmpresa";
        //
        //
        // $sql = $con_sql_server->prepare($sql);
        // $sql->bindParam('CdEmpresa', $info[$prefix.'CdEmpresa']);
        // $sql->bindParam('CdProduto', $info[$prefix.'CdProduto']);
        // $sql->bindParam('CdBarra', $info[$prefix.'CdBarra']);
        // $sql->bindParam('DsProduto', $info[$prefix.'DsProduto']);
        // $sql->bindParam('Unidade', $info[$prefix.'Unidade']);
        // $sql->bindParam('QtdeEmbalagem', $info[$prefix.'QtdeEmbalagem']);
        // $sql->bindParam('CustoReal', $info[$prefix.'CustoReal']);
        // $sql->bindParam('DtSincronizar', $info[$prefix.'DtSincronizar']);
        // $sql->bindParam('FlStatus', $info[$prefix.'FlStatus']);
        // $sql->bindParam('CDGRUPO', $info[$prefix.'CDGRUPO']);
        // $sql->bindParam('FlPadrao', $info[$prefix.'FlPadrao']);
        // $sql->bindParam('FlEspecial', $info[$prefix.'FlEspecial']);
        // $sql->bindParam('PeIcms', $info[$prefix.'PeIcms']);
        // $sql->bindParam('PeIpi', $info[$prefix.'PeIpi']);
        // $sql->bindParam('PeReducao', $info[$prefix.'PeReducao']);
        // $sql->bindParam('PeMargemLucro', $info[$prefix.'PeMargemLucro']);
        // $sql->bindParam('Unitario', $info[$prefix.'Unitario']);
        // $sql->bindParam('Medio', $info[$prefix.'Medio']);
        // $sql->bindParam('Saldo', $info[$prefix.'Saldo']);
        // $sql->bindParam('OldCdEmpresa', $info['old_CdEmpresa']);
        // $sql->bindParam('OldCdProduto', $info['old_CdProduto']);
        // $sql->execute();

    }
    catch(PDOException $e){
        $error_message = $e->getMessage();
        echo $error_message;
        exit;
    }

    echo 0;
}

if(@$_POST['action'] == "CarregaFotoDoProduto"){
    $id = $_POST['id'];
    $CdEmpresa = $_POST['CdEmpresa'];
    if(@file_exists(__DIR__.'/docs/'.$CdEmpresa.'-'.$id.'.jpg')){
        echo 1;
    }
    else {
        echo 0;
    }

    exit;
}
exit;
