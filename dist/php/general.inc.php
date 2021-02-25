<?php
ini_set('display_errors', 'on');
///////////////////////////////////////////
//VERSION
$VERSION = '0.1b';
///////////////////////////////////////////

//Autoload geral das classes
spl_autoload_register(function ($class_name) {
    include __DIR__ .'/class/'.$class_name . '.class.php';
});
//Dados de conexão ao banco de dados
if(!file_exists(__DIR__.'/general_connect.php')){
    echo 'Arquivo de configuração de banco de dados MySql não encontrado. Verifique com o suporte do sistema. Abortando.';
    exit;
}

require_once __DIR__.'/general_connect.php';

//Dados de configuração do sistema
if(!file_exists(__DIR__.'/config.php')){
    echo 'Arquivo de configuração do sistema não encontrado. Verifique com o suporte do sistema. Abortando.';
    exit;
}

require_once __DIR__.'/config.php';

// Conexão ao banco

try {
    $con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");
    $con_sql_server->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

    $con = $con_sql_server;
}
catch(Exception $e){
    $retorno['erro'] = 1;
    $retorno['erro_mensagem'] = "Erro ao conectar ao banco de dados central. Verifique com o suporte do sistema. Abortando.";
    echo json_encode($retorno);
	// echo 'Erro ao conectar ao banco de dados central. Verifique com o suporte do sistema. Abortando.';
	exit;
}
catch(PDOException $pdo){
    $retorno['erro'] = 1;
    $retorno['erro_mensagem'] = "Erro ao conectar ao banco de dados central. Verifique com o suporte do sistema. Abortando.";
    echo json_encode($retorno);
    	exit;
}

function utf8ize( $mixed ) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
    }
    return $mixed;
}


@$representante_id = $_SESSION["nobre_usuario_id"];
