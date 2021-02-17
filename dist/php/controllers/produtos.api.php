<?php
header('Access-Control-Allow-Origin: *');
session_start();
require __DIR__.'/../general.inc.php';
if($_POST['action'] == "busca_produtos"){
    $foto_dir = __DIR__.'/../../../content/pages/cadastros/cadastros/CadastroProdutos/docs/';
    $relative_foto_dir = "content/pages/cadastros/cadastros/CadastroProdutos/docs/";
    $tabela = $_POST['tabela'];
    $busca = $_POST['busca'];
    $con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
    $sql = new SqlServer($con_sql_server);
    $busca = $sql->BuscaProduto($busca, $tabela, false, 5);
    if(@$busca[0]){
        foreach($busca as $key=>$val){
            $nova_busca[$key] = $busca[$key];
            $imagem = $val['empresa'].'-'.$val['produto_id'].'.jpg';
            $arquivo_imagem_local = $foto_dir.$imagem;
            // echo $arquivo_imagem_local;
            if(@file_exists($arquivo_imagem_local)){
                $nova_busca[$key]['produto_imagem'] = '<center><img src="'.$relative_foto_dir.$imagem.'" style="max-width: 200px;"/></center>';
                // $nova_busca[$key]['produto_imagem'] = '<center><img src="'.$relative_foto_dir.'01-01.016-0.jpg" style="max-width: 200px;"/></center>';

            }
            else {
                $nova_busca[$key]['produto_imagem'] = '';

            }
        }
        $retorno['fetch'] = $nova_busca;
        $retorno['status'] = 'ok';
        echo json_encode(utf8ize($retorno));
    }
    else {
        $retorno['status'] = 'erro';
        echo json_encode(utf8ize($retorno));
    }
    exit;
}

if($_POST['action'] == "busca_produtos_id"){
    $tabela = $_POST['tabela'];
    $busca = $_POST['busca'];
    $con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
    $sql = new SqlServer($con_sql_server);
    $busca = $sql->BuscaProduto($busca, $tabela, true, 5);
    if(@$busca[0]){
        $retorno['fetch'] = $busca;
        $retorno['status'] = 'ok';
        echo json_encode(utf8ize($retorno));
    }
    else {
        $retorno['status'] = 'erro';
        echo json_encode(utf8ize($retorno));
    }
    exit;
}
