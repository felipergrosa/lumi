<?php
@session_start();
$user_code = $_SESSION['nobre_usuario_id'];
ini_set('display_errors', 'on');
// echo '<pre>';
require_once __DIR__.'/../../../../../dist/php/general.inc.php';
$path =  __DIR__.'/../../../../../';
$CdRepresentante = $_POST['CdRepresentante'];
echo "<pre>";
// var_dump($_POST);
// var_dump($CdRepresentante);


$sqlClass = new SqlServer($con_sql_server);

$empresas_permissionadas = $sqlClass->BuscaEmpresas($CdRepresentante);
// var_dump($empresas_permissionadas);
foreach($empresas_permissionadas as $empresa){
    $empresa_id = $empresa['id'];
    $ativos = $sqlClass->BuscaCondicoesPagamento($empresa_id, $CdRepresentante);
    foreach($ativos as $atv){
        $CondPgto['ativos'][$empresa_id][$atv['id']] = true;
    }
    // $CondPgto['ativos'][$empresa_id] = $sqlClass->BuscaCondicoesPagamento($empresa_id, $CdRepresentante);
    $CondPgto['todos'][$empresa_id] = $sqlClass->BuscaCondicoesPagamento($empresa_id);
}

// COND PGTO
// Municipio
// natureza op
// Prioridade
// Seg Mercado
// Tabela
// Regiao


?>
<table class="table table-hover table-striped table-bordered" id="cadastro_usuarios_edit_tabela_perm_condpgto" name="cadastro_usuarios_edit_tabela_perm_condpgto">
    <thead>
        <tr>
            <th>Cd</th>
            <th>Desc</th>
            <th>Empresa</th>
            <th>Minimo</th>
            <th>Perm</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach($CondPgto['todos'] as $empresa_id=>$cpt_e){
            foreach($cpt_e as $cpt){
                if(@$CondPgto['ativos'][$empresa_id][$cpt['id']]){
                    $perm = 'checked="checked"';
                }
                else {
                    $perm = "";
                }
                echo '<tr>' .
                '<td>'.$cpt['id'].'</td>' .
                '<td>'.$cpt['descricao'].'</td>' .
                '<td>'.$cpt['empresa_nome'].'</td>' .
                '<td>'.$cpt['minimo'].'</td>' .
                '<td><input value="'.$cpt['id'].'|'.$empresa_id.'" type="checkbox" '.$perm.' /></td>' .

                '</tr>';
            }
        }
        ?>
    </tbody>
</table>
