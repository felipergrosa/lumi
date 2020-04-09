
<?php
sleep(5);
@session_start();
ini_set('display_errors', 'on');
$user_code = $_SESSION['nobre_usuario_id'];
require_once __DIR__.'/../../../dist/php/general.inc.php';
$path =  __DIR__.'/../../..';
$ControleAcessos = new ControleAcessos($con, $user_code, $path);
$menu_controle = $ControleAcessos->menu;
$codigo_submenu = $_GET['cm'];
$codigo_submenu = explode('/',$codigo_submenu);
$menu = $codigo_submenu[0];
$submenu = $codigo_submenu[1];


$submenu_info = $menu_controle[$menu]['submenu'][$submenu];
$telas = $submenu_info['tela'];
$server = $_SERVER['HTTP_HOST'];
?>
<div class="divider"></div>
<div class="col-lg-12 col-xl-12">
    <div class="main-card mb-3 card">
        <div class="card-body"><h5 class="card-title"><?php echo  $submenu_info['nome'].' / '. $submenu_info['descricao']; ?></h5>
            <div class="grid-menu grid-menu-3col">
                <div class="no-gutters row">
                    <?php
                    foreach($telas as $key=>$tl){
                        $cod_pagina = $key;
                        echo '<div class="col-sm-6 col-xl-3">
                            <button onclick="javascript: CarregaPagina(\'content/pages/'.$menu.'/'.$submenu.'/'.$cod_pagina.'/'.$tl['link'].'\');" class="btn-icon-vertical btn-square btn-transition btn '.$tl['class'].'"><i class="'.$tl['style'].' btn-icon-wrapper"> </i>'.$tl['nome'].'</button>
                        </div>';
                    }
                    ?>

                </div>
            </div>

        </div>
    </div>
</div>
