<?php

class ControleAcessos {

    public $user;
    public $isroot;
    public $menu;
    public $acessos;
    public $con_front;
    public $acessos_complementar;
    public $filas;
    public $operadoras;
    public $campanhas;
    public $listas;
    public $path;
    public $lang;


    function ControleAcessos($con_front, $user, $path) {
        $this->con_front = $con_front;
        $this->user = $user;
        $this->DefineRoot();
        $this->MontaMenu($path);
        // $this->GetFilas();

        @session_start();
        $this->lang = $_SESSION['LANGUAGE_VALUE'];
        // Manter o GetCampanhasListas comentado e só requisitar no momento do uso, o mesmo consome demais do banco e do sistema.
        // $this->GetCampanhasListas();
    }

    function DefineRoot() {
        $con_front = $this->con_front;
        $user = $this->user;
        $sql_usuarios = "SELECT general_level FROM usuarios WHERE id = '$user'";
        $sql_usuarios = $con_front->query($sql_usuarios);
        $row = $sql_usuarios->fetch(PDO::FETCH_ASSOC);
        $general_level = $row['general_level'];
        if ($general_level == 'ROOT') {
            $this->isroot = true;
        } else {
            $this->isroot = false;
        }
    }

    function MontaMenu($dir) {
        $usuario = $this->user;
        $isroot = $this->isroot;
        $con = $this->con_front;
        $percorrer = $dir.'/content/pages/';
        //lê diretorio
        $menu_folder = opendir($percorrer);
        //verre diretorio
        while($pastas = readdir($menu_folder)){
            // exclui lixo da listagem e verifica se é uma pasta o item atual
            if($pastas != '..' && $pastas != '.' && is_dir($percorrer.'/'.$pastas)){
                //define nome do array do menu a ser tratado
                $menu_name = $pastas;

                //Verifica se existe config.xml
                if(file_exists($percorrer.'/'.$menu_name.'/config.xml')){
                    //Puxa os dados do config.xml
                    $detalhes_menu = $this->xmlRead($percorrer.'/'.$menu_name.'/config.xml', 'arquivo', 'array');
                    $detalhes_menu = $detalhes_menu['display'];

                    if($detalhes_menu['atividade'] == 1){
                        $menu[$menu_name] = $detalhes_menu;

                        //Busca por submenus dentro do menu
                        $percorrer_menu = $percorrer.$menu_name.'/';
                        $percorrer_menu_folder = opendir($percorrer_menu);
                        while($pastas_menu = readdir($percorrer_menu_folder)){
                            if($pastas_menu != '.' && $pastas_menu != '..' && is_dir($percorrer_menu.$pastas_menu)){
                                $submenu_name = $pastas_menu;
                                if(file_exists($percorrer_menu.$pastas_menu.'/config.xml')){
                                    $detalhes_submenu = $this->xmlRead($percorrer_menu.$pastas_menu.'/config.xml', 'arquivo', 'array');
                                    $detalhes_submenu = $detalhes_submenu['display'];
                                    $menu[$menu_name]['submenu'][$submenu_name] = $detalhes_submenu;

                                    //Busca telas dentro do submenu
                                    $percorrer_submenu = $percorrer_menu.$submenu_name.'/';
                                    $percorrer_submenu_folder = opendir($percorrer_submenu);
                                    while($pastas_submenu = readdir($percorrer_submenu_folder)){
                                        $tela_nome = $pastas_submenu;
                                        if($pastas_submenu != '.' && $pastas_submenu != '..' && is_dir($percorrer_submenu.$pastas_submenu)){
                                            if(file_exists($percorrer_submenu.$pastas_submenu.'/config.xml')){
                                                $detalhes_tela = $this->xmlRead($percorrer_submenu.$pastas_submenu.'/config.xml', 'arquivo', 'array');
                                                if($detalhes_tela['display']['atividade'] == 1){
                                                    $detalhes_tela = $detalhes_tela['display'];
                                                    $menu[$menu_name]['submenu'][$submenu_name]['tela'][$tela_nome] = $detalhes_tela;
                                                    $full_acesso = $menu_name.'/'.$pastas_menu.'/'.$pastas_submenu;
                                                    $verifica_acesso = "SELECT * FROM usuarios_acessos WHERE usuario = '$usuario' && tela = '$full_acesso'";

                                                    $verifica_acesso = $con->query($verifica_acesso);
                                                    if($verifica_acesso->rowCount() > 0 or @$isroot){
                                                        $acessos_geral[$full_acesso] = $full_acesso;
                                                        $acessos[$menu_name]['submenu'][$submenu_name]['tela']['acesso'][$tela_nome] = true;

                                                    }
                                                    else {
                                                      if(!$isroot){
                                                        unset($menu[$menu_name]['submenu'][$submenu_name]['tela'][$tela_nome]);
                                                      }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                }

                            }
                        }
                    }


                }
            }




        }

        //Apaga as variáveis de telas sem acesso caso o usuario não seja ROOT.
        if (@!$isroot) {
            foreach ($menu as $key=>$mn) {
                $cont = 0;
                $target = $key;
                foreach ($mn['submenu'] as $key=>$submn) {
                    //faz a soma se existem telas no submenu, senão apaga
                    $cont2 = 0;
                    $alvosub = $key;



                    if(@is_array($submn)){
                        if(@is_array($acessos[$target]['submenu'][$key]['tela']['acesso'])){
                            foreach ($acessos[$target]['submenu'][$key]['tela']['acesso'] as $ta) {
                                if ($ta) {
                                    $cont++;
                                    $cont2++;
                                }
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
        $this->menu = $menu;
        @ $this->acessos = $acessos_geral;
    }



    function ExibeMenu() {

        $lang = $this->lang;
        $menu = $this->menu;
        ksort($menu);
        foreach ($menu as $key_menu=>$mn) {
            if ($mn['link'] != null) {
                $click = 'onclick="javascript: CarregaPagina(\'' . $mn['link'] . '\'); "';
                $seta = '';
            } else {
                $click = '';
                $seta = '<i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>';
            }
            if (count(@$mn['submenu']) == 1) {
                foreach ($mn['submenu'] as $key_submenu=>$submenu) {
                echo '<li class="mm-active">
                    <a href="#" onclick="javascript: CarregaPagina(\'content/pages/controle/gpmenu.php?cm='.$key_menu.'/'.$key_submenu.'\'); ">
                        <i class="' . $mn['class'] . ' "></i> <span>' . $mn['nome'] . '</span>

                    </a></li>';


                }
            } else {

                echo '<li class="mm-active">
                <a href="#" ' . $click . '>
                    <i class="' . $mn['class'] . '"></i> <span>' .$mn['nome']. '</span>
                    ' . $seta . '
                </a>';


                if (@$mn['submenu'] && @$mn['link'] == null) {
                    ksort($mn['submenu']);
                    echo '<ul>';
                    foreach ($mn['submenu'] as $key_submenu=>$submenu) {
                        echo '<li> <a href="#" onclick="javascript: CarregaPagina(\'content/pages/controle/gpmenu.php?cm='.$key_menu.'/'.$key_submenu.'\')"><i class="' . $submenu['class'] . '"></i> ' . $submenu['nome'] . '</a></li>';
                    }
                    echo '</ul>';
                }
            }
            echo '</li>';
        }
    }

    function Dump() {
        var_dump($this->campanhas);
    }

    function VerificaAcessoATela($tela_master){
        $menu = $this->menu;
        $isroot = $this->isroot;
        $criadorcodigo = $this->user;
        $con = $this->con_front;
        //Se for root não importa que tela seja, esta permissionado.
        if($isroot){
            return true;
        }
       /*
        PODE SER FEITO EM SQL SE A SESSAO OU O ARRAY APRESENTAREM ERROS.

        $sql = "SELECT * FROM configuracoes_acessos_menus a
        INNER JOIN usuarios_acessos b ON b.tela=a.id and b.usuario='$criadorcodigo'
        WHERE a.link = '$tela' ";
        $sql = $con->query($sql);
        if($sql->rowCount() > 0){
            return true;
        }
        else {
            return false;
        } */


        //Preferencia para fazer no array do SESSION, assim não sobrecarrega o banco.
        $permissao = false;
        foreach($menu as $mn){
            foreach($mn['submenu'] as $submenu){
                foreach($submenu['tela'] as $tela){
                    if(@$tela['link'] == $tela_master){
                        return $tela['link'];
                        $permissao = true;
                    }
                }
            }
        }

        return $permissao;

    }

    //Acessos complementar sem filtro, em array
    function GetAcessosComplementar(){
        $con = $this->con_front;
        $usuario = $this->user;
        $sql = "SELECT * FROM fla_usuario_contact_configuracoes_acessos_complementar WHERE usuario = '$usuario'";
        $sql = $con->query($sql);
        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
            $retorno[$row['id']][$row['tipo']] = $row['valor'];
        }
        return $retorno;
    }

    //Acessos complementar com filtro de filas, em objeto
    function GetFilas(){
        $con = $this->con_front;
        $usuario = $this->user;
        if($this->isroot == false){
            $sql = "SELECT * FROM fla_usuario_contact_configuracoes_acessos_complementar WHERE usuario = '$usuario' && tipo = 'fila'";
            $sql = $con->query($sql);
            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                $retorno[$row['valor']] = true;
            }
        }
        else {
            $sql = "SELECT fila FROM fla_fila_master";
            $sql = $con->query($sql);
            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                $retorno[$row['fila']] = $row['fila'];
            }
        }
        $this->filas = @$retorno;
    }
    function GetOperadoras(){
        $con = $this->con_front;
        $usuario = $this->user;
        if($this->isroot == false){
            $sql = "SELECT * FROM fla_usuario_contact_configuracoes_acessos_complementar WHERE usuario = '$usuario' && tipo = 'operadora'";
            $sql = $con->query($sql);
            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                $retorno[$row['valor']] = true;
            }
        }
        else {
            $sql = "SELECT cod_operadora FROM fla_operadoras";
            $sql = $con->query($sql);
            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                $retorno[$row['cod_operadora']] = true;
            }
        }
        $this->operadoras = @$retorno;
    }
    function GetCampanhasListas(){
        $con = $this->con_front;
        // Get campanhas
        $filas = $this->filas;
        if(@is_array($filas)){
            foreach($filas as $fila=>$value){
                $sql = "SELECT campanha FROM fla_fila_master WHERE fila = '$fila'";
                $sql = $con->query($sql);
                $row = $sql->fetch(PDO::FETCH_ASSOC);
                $campanhas[$row['campanha']] = $row['campanha'];
            }
        }
        else {
            $sql = "SELECT campanha FROM fla_fila_master WHERE fila = '$filas'";
            $sql = $con->query($sql);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $campanhas[$row['campanha']] = $row['campanha'];
        }

        $this->campanhas = $campanhas;

        foreach($campanhas as $cp){
            $cp = trim($cp);
            $sql = "SELECT lista FROM fla_lista_master WHERE campanha = '$cp'";
            $sql = $con->query($sql);
            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                $listas[$row['lista']] = $row['lista'];
            }
        }
        @ $this->listas = $listas;
    }

    function XmlRead($content, $tipo = 'arquivo', $saida = 'object'){
        if($tipo == 'arquivo'){
            if(!file_exists($content)){
                return 'Erro: Arquivo não existe';
            }
            $xml_object = simplexml_load_file($content);
        }
        elseif($tipo == 'string'){
            $xml_object = simplexml_load_string($content);
        }
        if($saida == 'object'){
            return $xml_object;
        }
        elseif($saida == 'array'){
            $xml_object = json_encode($xml_object);
            $xml_object = json_decode($xml_object, TRUE);
            return $xml_object;
        }
        elseif($saida == 'json'){
            $xml_object = json_encode($xml_object);
            return $xml_object;
        }
        return 0;
    }
    public static function VerificaAcessoATelaNovo($acessos, $tela){
        $acerto = 0;
        foreach($acessos as $key=>$value){

            $bater = substr($tela, (strlen($tela)-strlen($key)));
            if($bater == $key){
                $acerto = 1;
            }


        }
        if($acerto === 0){
            echo"<script type='text/javascript'>";
                        echo "alert('Você não tem permissão para ver esta tela.');
                        window.close();";
                    echo "</script>";


                    exit;
        }
    }

    public static function SalvaLog($con, $usuario, $link, $post, $get, $action = false){
        $tela2 = explode('/', $link);
        foreach($tela2 as $tl){
            $tela = $tl;
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        try {
            $sql = "INSERT INTO fla_usuario_contact_historico
            (usuario, ip, url, tela, action, post, get, data) VALUES
            (:usuario, :ip, :link, :tela, :action,
            :post,:get, NOW())";
            $sql = $con->prepare($sql);
            $sql->bindParam('usuario', $usuario);
            $sql->bindParam('ip', $ip);
            $sql->bindParam('link', $link);
            $sql->bindParam('tela', $tela);
            $sql->bindParam('action', $action);
            $sql->bindParam('post', json_encode($post));
            $sql->bindParam('get', json_encode($get));
            $sql->execute();
        } catch(PDOException $exception){
            return $exception->getMessage();
        }
    }


}
