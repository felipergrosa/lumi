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

foreach($empresas_permissionadas as $empresa){
    $empresa_nome[$empresa['id']] = $empresa['nome'];
}

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
    <table class="table table-hover table-striped table-bordered" id="cadastro_usuarios_edit_tabela_perm_regiao" name="cadastro_usuarios_edit_tabela_perm_regiao">
        <thead>
            <tr>
                <th>Cd</th>
                <th>Regi??o</th>
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
if(@$_POST['action'] == 'prio'){

    ?>
    <table class="table table-hover table-striped table-bordered" id="cadastro_usuarios_edit_tabela_perm_prio" name="cadastro_usuarios_edit_tabela_perm_prio">
        <thead>
            <tr>
                <th>Empresa</th>
                <th>0</th>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($empresas_permissionadas as $empresa){
                $empresa_id = $empresa['id'];
                $ativos = $sqlClass->BuscaPermPrioridade($empresa_id, $CdRepresentante);
                $ativos = $ativos[0];
                if($ativos['p0'] == "S"){
                    $p0 = '<input value="'.$empresa_id.'|p0" type="checkbox" checked="checked" />';
                }
                else {
                    $p0 = '<input value="'.$empresa_id.'|p0" type="checkbox" />';
                }

                if($ativos['p1'] == "S"){
                    $p1 = '<input value="'.$empresa_id.'|p1" type="checkbox" checked="checked" />';
                }
                else {
                    $p1 = '<input value="'.$empresa_id.'|p1" type="checkbox" />';
                }

                if($ativos['p2'] == "S"){
                    $p2 = '<input value="'.$empresa_id.'|p2" type="checkbox" checked="checked" />';
                }
                else {
                    $p2 = '<input value="'.$empresa_id.'|p2" type="checkbox" />';
                }

                if($ativos['p3'] == "S"){
                    $p3 = '<input value="'.$empresa_id.'|p3" type="checkbox" checked="checked" />';
                }
                else {
                    $p3 = '<input value="'.$empresa_id.'|p3" type="checkbox" />';
                }

                if($ativos['p4'] == "S"){
                    $p4 = '<input value="'.$empresa_id.'|p4" type="checkbox" checked="checked" />';
                }
                else {
                    $p4 = '<input value="'.$empresa_id.'|p4" type="checkbox" />';
                }

                    echo '<tr>' .
                    '<td>'.$empresa_nome[$empresa_id].'</td>' .
                    '<td>'.$p0.'</td>' .
                    '<td>'.$p1.'</td>' .
                    '<td>'.$p2.'</td>' .
                    '<td>'.$p3.'</td>' .
                    '<td>'.$p4.'</td>' .


                    '</tr>';
                }
            ?>
        </tbody>
    </table>
<?php
exit;
}
if(@$_POST['action'] == 'seg_merc'){
        $ativos = $sqlClass->BuscaSegmentoMercado($CdRepresentante);
        foreach($ativos as $atv){
            $SegmentoMercado['ativos'][$atv['CdSegmento']] = true;
        }
        $SegmentoMercado['todos'] = $sqlClass->BuscaSegmentoMercado();

    ?>
    <table class="table table-hover table-striped table-bordered" id="cadastro_usuarios_edit_tabela_perm_seg_merc" name="cadastro_usuarios_edit_tabela_perm_seg_merc">
        <thead>
            <tr>
                <th>Cd</th>
                <th>Segmento</th>
                <th>Perm</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($SegmentoMercado['todos'] as $arr){
                    if(@$SegmentoMercado['ativos'][$arr['CdSegmento']]){
                        $perm = 'checked="checked"';
                    }
                    else {
                        $perm = "";
                    }
                    echo '<tr>' .
                    '<td>'.$arr['CdSegmento'].'</td>' .
                    '<td>'.$arr['DsSegmento'].'</td>' .
                    '<td><input value="'.$arr['CdSegmento'].'" type="checkbox" '.$perm.' /></td>' .

                    '</tr>';
                }
            ?>
        </tbody>
    </table>
<?php
exit;
}
if(@$_POST['action'] == 'tabela'){
foreach($empresas_permissionadas as $empresa){
    $empresa_id = $empresa['id'];
    $ativos = $sqlClass->BuscaTabelas($empresa_id, $CdRepresentante);
    foreach($ativos as $atv){
        $Tabelas['ativos'][$empresa_id][$atv['id_tabela']] = true;
    }
    $Tabelas['todos'][$empresa_id] =  $sqlClass->BuscaTabelas($empresa_id);
}


    ?>
    <table class="table table-hover table-striped table-bordered" id="cadastro_usuarios_edit_tabela_perm_tabela" name="cadastro_usuarios_edit_tabela_perm_tabela">
        <thead>
            <tr>
                <th>Cd</th>
                <th>Tabela</th>
                <th>Empresa</th>
                <th>Perm</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($Tabelas['todos'] as $empresa_id=>$arr_emp){
                foreach($arr_emp as $arr){
                        if(@$Tabelas['ativos'][$empresa_id][$arr['id_tabela']]){
                            $perm = 'checked="checked"';
                        }
                        else {
                            $perm = "";
                        }
                        echo '<tr>' .
                        '<td>'.$arr['id_tabela'].'</td>' .
                        '<td>'.$arr['descricao_tabela'].'</td>' .
                        '<td>'.$empresa_nome[$arr['CdEmpresa']].'</td>' .

                        '<td><input value="'.$arr['id_tabela'].'|'.$empresa_id.'" type="checkbox" '.$perm.' /></td>' .

                        '</tr>';
                    }
                }
            ?>
        </tbody>
    </table>
<?php
exit;
}
?>
