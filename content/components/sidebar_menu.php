<ul class="vertical-nav-menu">
    <li class="app-sidebar__heading">Menu</li>
<?php
@session_start();
$user_code = $_SESSION['nobre_usuario_id'];
require_once __DIR__.'/../../dist/php/general.inc.php';
$path =  __DIR__.'/../..';
@$menu = new ControleAcessos($con, $user_code, $path);
$menu->ExibeMenu();

?>
<!-- <li class="mm-active">
    <a href="#">
        <i class="metismenu-icon pe-7s-tools"></i>
        Cadastros
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>
    <ul>
        <li>
            <a href="index.html" class="mm-active">
                <i class="metismenu-icon">
                </i>Usuários
            </a>
        </li>
        <li>
            <a href="dashboards-commerce.html">
                <i class="metismenu-icon">
                </i>Configurações
            </a>
        </li>

    </ul>
</li> -->
</ul>
