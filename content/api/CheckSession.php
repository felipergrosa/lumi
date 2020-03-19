<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__.'/../../dist/php/general.inc.php';
@session_start();
if($_SESSION['sig4_usuario_id']){
    $retorno = true;
}
else {
    $retorno = false;
}
echo json_encode($retorno);
exit;
