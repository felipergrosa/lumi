<?php
@session_start();
$user_code = $_SESSION['nobre_usuario_id'];
ini_set('display_errors', 'on');
// echo '<pre>';
require_once __DIR__.'/../../../../../dist/php/general.inc.php';
$usuario_alvo = $_POST['CdRepresentante'];
$path =  __DIR__.'/../../../../../';
$ControleAcessos = new ControleAcessos($con_sql_server, $user_code, $path);
$isroot = $ControleAcessos->isroot;
$menu = $ControleAcessos->menu;
// var_dump($menu);
// echo '</pre>';
$edittela = true;

ksort($menu);

// exit;

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
    if (@$mn['link'] != null) {
        $click = 'onclick="javascript: CarregaPagina(\'' . @$mn['link'] . '\'); "';
        $seta = '';
    } else {
        $click = '';
        $seta = '<i class="fa fa-angle-left pull-right"></i>';
    }
    echo '<ul id="editmenu' . $mn['id'] . '">';
    echo '<li><span ><span style="font-weight: bold;">' . $mn['nome'] . '</span></li>
    <ul>';
    if ($mn['submenu']) {
        foreach ($mn['submenu'] as $key=>$submenu) {
            ksort($submenu['tela']);
            $submenu['id'] = $key;
            echo '<li><span >' . $submenu['nome'] . '</li>
            <ul id="editmenusub' . $submenu['id'] . '">';
            if ($submenu['tela']) {
                foreach ($submenu['tela'] as $key=>$tela) {
                    $tela['id'] = $key;
                    if ($edittela) {
                        $telathis = $mn['id'].'/'.$submenu['id'].'/'.$tela['id'];
                        // var_dump($telathis);
                        if (!@$tipogroup) {
                            $sql5 = "SELECT * FROM BusinessCadPermiAcessos WHERE usuario = '$usuario_alvo' AND tela = '$telathis'";

                        } else {
                            $sql5 = "SELECT * FROM BusinessCadPermiAcessos WHERE usuario = '$usuario_alvo' AND tela = '$telathis'";
//                            echo $sql5;
                        }
                        $sql5 = $con->query($sql5);
                        if ($sql5->rowCount() != 0) {
                            echo '<li><label><input type="checkbox" checked="checked" id="tela' . $tela['id'] . '" name="tela[' . $tela['id'] . ']" value="' . $mn['id'].'/'.$submenu['id'].'/'. $tela['id'] . '" onclick="PermissionaTelaSistema(\''.$usuario_alvo.'\', \''.$tela['nome'].'\', this);">' . $tela['nome'] . '</label></li>';
                        } else {
                            echo '<li><label><input type="checkbox" id="tela' . $tela['id'] . '" name="tela[' . $tela['id'] . ']" value="' . $mn['id'].'/'.$submenu['id'].'/'. $tela['id'] . '" onclick="PermissionaTelaSistema(\''.$usuario_alvo.'\', \''.$tela['nome'].'\', this);">' . $tela['nome'] . '</label></li>';
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
    function PermissionaTelaSistema(CdRepresentante, tela, element){
        var checked = $(element).prop('checked');
        var tela = $(element).val();
        $.ajax({
            type: 'POST',
            url: 'content/pages/cadastros/usuarios/CadastroUsuarios/post.php',
            data: {'action': 'permissao_front', 'CdRepresentante':CdRepresentante, 'checked':checked, 'tela':tela},
            success: function (data)
            {
                console.log(data);
                toastr.success('Permissão Aplicada');
            }
        });
    }
</script>
