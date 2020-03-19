<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__.'/../../dist/php/general.inc.php';
@session_start();
echo json_encode($_SESSION);
exit;
