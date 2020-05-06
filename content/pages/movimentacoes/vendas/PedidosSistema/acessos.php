<?php
@session_start();
$user_code = $_SESSION['nobre_usuario_id'];
ini_set('display_errors', 'on');
// echo '<pre>';
require_once __DIR__.'/../../../../../dist/php/general.inc.php';
$usuario_alvo = $_POST['id'];
$path =  __DIR__.'/../../../../../';
$ControleAcessos = new ControleAcessos($con, $user_code, $path);
$isroot = $ControleAcessos->isroot;
$menu = $ControleAcessos->menu;
// var_dump($menu);
// echo '</pre>';
$edittela = true;

ksort($menu);

if (!@$isroot) {
    foreach ($menu as $mn) {
        $cont = 0;
        @$target = $mn['id'];
        foreach ($mn['submenu'] as $submn) {
            //faz a soma se existem telas no submenu, senão apaga
            $cont2 = 0;
            @$alvosub = $submn['id'];
            if (@$submn['telaacesso']['acesso']) {
                foreach ($submn['telaacesso']['acesso'] as $ta) {
//                    var_dump($ta);
                    if ($ta) {
                        $cont++;
                        $cont2++;
                    }
                }
            }
            //apaga submenu
            if ($cont2 == 0) {
                unset($menu[$target]['submenu'][$alvosub]);
            }
        }
        //apaga o menu
        if ($cont == 0) {
            unset($menu[$target]);
        }
    }
}

foreach ($menu as $key=>$mn) {
    $mn['id'] = $key;
    ksort($mn['submenu']);
    if ($mn['link'] != null) {
        $click = 'onclick="javascript: CarregaPagina(\'' . $mn['link'] . '\'); "';
        $seta = '';
    } else {
        $click = '';
        $seta = '<i class="fa fa-angle-left pull-right"></i>';
    }
    echo '<ul id="editmenu' . $mn['id'] . '">';
    echo '<li><span ><input type="checkbox" id="menu' . $mn['id'] . '" onclick="MarcaFilho(this);"><span style="font-weight: bold;">' . $mn['nome'] . '</span></li>
    <ul>';
    if ($mn['submenu']) {
        foreach ($mn['submenu'] as $key=>$submenu) {
            ksort($submenu['tela']);
            $submenu['id'] = $key;
            echo '<li><span ><input type="checkbox" id="submenu' . $submenu['id'] . '" onclick="MarcaFilho(this);">' . $submenu['nome'] . '</li>
            <ul id="editmenusub' . $submenu['id'] . '">';
            if ($submenu['tela']) {
                foreach ($submenu['tela'] as $key=>$tela) {
                    $tela['id'] = $key;
                    if ($edittela) {
                        $telathis = $mn['id'].'/'.$submenu['id'].'/'.$tela['id'];
                        if (!@$tipogroup) {
                            $sql5 = "SELECT * FROM usuarios_acessos WHERE usuario = '$usuario_alvo' && tela = '$telathis'";

                        } else {
                            $sql5 = "SELECT * FROM usuarios_group_acessos WHERE `group` = '$usuario_alvo' && tela = '$telathis'";
//                            echo $sql5;
                        }
                        $sql5 = $con->query($sql5);
                        if ($sql5->rowCount() > 0) {
                            echo '<li><label><input type="checkbox" checked="checked" id="tela' . $tela['id'] . '" name="tela[' . $tela['id'] . ']" value="' . $mn['id'].'/'.$submenu['id'].'/'. $tela['id'] . '">' . $tela['nome'] . '</label></li>';
                        } else {
                            echo '<li><label><input type="checkbox" id="tela' . $tela['id'] . '" name="tela[' . $tela['id'] . ']" value="' . $mn['id'].'/'.$submenu['id'].'/'. $tela['id'] . '">' . $tela['nome'] . '</label></li>';
                        }
                    } else {
                        echo '<li><label><input type="checkbox" id="tela' . $tela['id'] . '" name="tela[' . $tela['id'] . ']" value="' . $tela['id'] . '">' . $tela['nome'] . '</label></li>';
                    }
                    // echo '<br />';
                }

            }
            echo '</ul>';
        }
        echo '</ul>';

    }
    echo '</ul>';
    // echo '</span><br /><hr />';
}
// echo '</ul>';
?>
<script type="text/javascript">
    function MarcaFilho(element) {
        var elementpai = $(element).parent();
        console.log(elementpai);
        var elementousado = $(elementpai).attr('id');
        if ($(element).prop('checked') == true) {
            $('#' + elementousado + ' :input').each(function (k, v) {
                $(v).prop('checked', true);
            });
        } else {
            $('#' + elementousado + ' :input').each(function (k, v) {
                $(v).prop('checked', false);
            });
        }


    }
</script>
