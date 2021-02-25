<?php
@session_start();
$user_code = $_SESSION['nobre_usuario_id'];
ini_set('display_errors', 'on');
// echo '<pre>';
require_once __DIR__.'/../../../../../dist/php/general.inc.php';
$path =  __DIR__.'/../../../../../';
$CdRepresentante = $_POST['CdRepresentante'];
// echo "<pre>";
// var_dump($_POST);
// var_dump($CdRepresentante);


$sqlClass = new SqlServer($con_sql_server);

$empresas_permissionadas = $sqlClass->BuscaEmpresas($CdRepresentante);
// var_dump($empresas_permissionadas);



// COND PGTO
// Municipio
// Regiao

// natureza op
// Prioridade
// Seg Mercado
// Tabela




if(@$_POST['action'] == 'cond_pgto'){
foreach($empresas_permissionadas as $empresa){
    $empresa_id = $empresa['id'];
    $ativos = $sqlClass->BuscaCondicoesPagamento($empresa_id, $CdRepresentante);
    foreach($ativos as $atv){
        $CondPgto['ativos'][$empresa_id][$atv['id']] = true;
    }
    // $CondPgto['ativos'][$empresa_id] = $sqlClass->BuscaCondicoesPagamento($empresa_id, $CdRepresentante);
    $CondPgto['todos'][$empresa_id] = $sqlClass->BuscaCondicoesPagamento($empresa_id);
}



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

<?php
exit;
}
if(@$_POST['action'] == 'municipios'){
        $ativos = $sqlClass->BuscaMunicipios($CdRepresentante);
        foreach($ativos as $atv){
            $Municipios['ativos'][$atv['CdMunicipio']] = true;
        }
        $Municipios['todos'] = $sqlClass->BuscaMunicipios();

    ?>
    <table class="table table-hover table-striped table-bordered" id="cadastro_usuarios_edit_tabela_perm_municipios" name="cadastro_usuarios_edit_tabela_perm_municipios">
        <thead>
            <tr>
                <th>Cd</th>
                <th>Municipio</th>
                <th>Estado</th>
                <th>Perm</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($Municipios['todos'] as $arr){
                    if(@$Municipios['ativos'][$arr['CdMunicipio']]){
                        $perm = 'checked="checked"';
                    }
                    else {
                        $perm = "";
                    }
                    echo '<tr>' .
                    '<td>'.$arr['CdMunicipio'].'</td>' .
                    '<td>'.$arr['DsMunicipio'].'</td>' .
                    '<td>'.$arr['Estado'].'</td>' .
                    '<td><input value="'.$arr['CdMunicipio'].'" type="checkbox" '.$perm.' /></td>' .

                    '</tr>';
                }
            ?>
        </tbody>
    </table>
<?php
exit;
}

if(@$_POST['action'] == 'regiao'){
        $ativos = $sqlClass->BuscaRegiao($CdRepresentante);
        foreach($ativos as $atv){
            $Regiao['ativos'][$atv['CdRegiao']] = true;
        }
        $Regiao['todos'] = $sqlClass->BuscaRegiao();

    ?>
    <table class="table table-hover table-striped table-bordered" id="cadastro_usuarios_edit_tabela_perm_municipios" name="cadastro_usuarios_edit_tabela_perm_municipios">
        <thead>
            <tr>
                <th>Cd</th>
                <th>Região</th>
                <th>Perm</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($Regiao['todos'] as $arr){
                    if(@$Regiao['ativos'][$arr['CdRegiao']]){
                        $perm = 'checked="checked"';
                    }
                    else {
                        $perm = "";
                    }
                    echo '<tr>' .
                    '<td>'.$arr['CdRegiao'].'</td>' .
                    '<td>'.$arr['DsRegiao'].'</td>' .
                    '<td><input value="'.$arr['CdRegiao'].'" type="checkbox" '.$perm.' /></td>' .

                    '</tr>';
                }
            ?>
        </tbody>
    </table>
<?php
exit;
}
if(@$_POST['action'] == 'nat_op'){
        $ativos = $sqlClass->BuscaNaturezaOperacao(0, $CdRepresentante);
        foreach($ativos as $atv){
            $NaturezaOperacao['ativos'][$atv['id']] = true;
        }
        $NaturezaOperacao['todos'] = $sqlClass->BuscaNaturezaOperacao();

    ?>
    <table class="table table-hover table-striped table-bordered" id="cadastro_usuarios_edit_tabela_perm_nat_op" name="cadastro_usuarios_edit_tabela_perm_nat_op">
        <thead>
            <tr>
                <th>Cd</th>
                <th>Natureza</th>
                <th>Perm</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($NaturezaOperacao['todos'] as $arr){
                    if(@$NaturezaOperacao['ativos'][$arr['id']]){
                        $perm = 'checked="checked"';
                    }
                    else {
                        $perm = "";
                    }
                    echo '<tr>' .
                    '<td>'.$arr['id'].'</td>' .
                    '<td>'.$arr['descricao'].'</td>' .
                    '<td><input value="'.$arr['id'].'" type="checkbox" '.$perm.' /></td>' .

                    '</tr>';
                }
            ?>
        </tbody>
    </table>
<?php
exit;
}
?>
