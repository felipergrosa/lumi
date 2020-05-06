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
	$con = new PDO("mysql:host=$db_hostname;dbname=$db_database", $db_username, $db_password,
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'", PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION));
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(Exception $e){
	echo 'Erro ao conectar ao banco de dados central. Verifique com o suporte do sistema. Abortando.';
	exit;
}
