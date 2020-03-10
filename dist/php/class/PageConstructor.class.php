<?php
class PageConstructor {
    public $path;


    function PageConstructor($path, $con){
        if(!file_exists($path)){
            return 'Path Não existe';
        }
        else {
            $this->path = $path;
            $this->con = $con;
        }
    }

    function CheckSql($show_queries = false){
        $path = $this->path;
        $con = $this->con;
        $errors = 0;
        //Verificar o arquivo tabelas
        $arquivo_tabelas = 'tables.php';
        if(file_exists($path.'sql/'.$arquivo_tabelas)){
            $abrir = fopen($path.'sql/'.$arquivo_tabelas, 'r');
            while(!feof($abrir)){
                unset($create_table_arquivo);
                //Caso exista a linha da tabela ja com o trim aplicado
                $tabela = trim(fgets($abrir, 4096));
                if($tabela != ''){
                    //Verificação se tabela existe
                    $sql = "SHOW TABLES LIKE '$tabela'";
                    $sql = $con->query($sql);
                    if($sql->rowCount() > 0){
                        //Comparador1 - Banco de dados local
                        $sql = "SHOW CREATE TABLE $tabela";
                        $sql = $con->query($sql);
                        $row = $sql->fetchAll(PDO::FETCH_ASSOC);
                        $create_table_banco = $row[0]['Create Table'];
                        $create_table_banco = explode('AUTO_INCREMENT=', $create_table_banco);
                        $create_table_banco = $create_table_banco[0];
                        $create_table_banco = trim($create_table_banco);
                        if($show_queries){
                            $retorno[$tabela]['banco'] = $create_table_banco;
                        }
                        //Comparador2 - Arquivo com create table da matriz
                        $file = $path.'sql/tables/'.$tabela;
                        //caso exista o arquivo gerado
                        if(file_exists($file)){
                            $abrir_tabela = fopen($file, 'r');
                            $create_table_arquivo = '';
                            while(!feof($abrir_tabela)){
                                $create_table_arquivo .= fgets($abrir_tabela, 4096);
                            }
                        }
                        @$create_table_arquivo = trim($create_table_arquivo);
                        if($show_queries){
                            $retorno[$tabela]['arquivo'] = $create_table_arquivo;
                        }
                        if($create_table_banco == $create_table_arquivo){
                            $retorno[$tabela]['resultado'] = true;
                        }
                        else {
                            $retorno[$tabela]['resultado'] = false;
                            $errors++;
                        }
                    }
                    else {
                        $file = $path.'sql/tables/'.$tabela;
                        //caso exista o arquivo gerado
                        if(file_exists($file)){
                            $abrir_tabela = fopen($file, 'r');
                            $create_table_arquivo = '';
                            while(!feof($abrir_tabela)){
                                $create_table_arquivo .= fgets($abrir_tabela, 4096);
                            }
                        }
                        @$create_table_arquivo = trim($create_table_arquivo);
                        if($show_queries){
                            $retorno[$tabela]['arquivo'] = $create_table_arquivo;
                        }
                        $retorno[$tabela]['banco'] = 'Tabela Inexistente';
                        $retorno[$tabela]['resultado'] = false;
                        $errors++;
                    }

                }

            }
            fclose($abrir);
        }


        //Procedures
        $arquivo_proc = 'procedures.php';
        if(file_exists($path.'sql/'.$arquivo_proc)){
            $abrir = fopen($path.'sql/'.$arquivo_proc, 'r');
            while(!feof($abrir)){
                unset($create_table_arquivo);
                //Caso exista a linha da tabela ja com o trim aplicado
                $tabela = trim(fgets($abrir, 4096));
                if($tabela != ''){
                    //Verifica se a procedure existe
                    $sql = "SELECT * FROM mysql.proc WHERE name = '$tabela'";
                    $sql = $con->query($sql);
                    if($sql->rowCount() > 0){
                        //Comparador1 - Banco de dados local
                        $sql = "SHOW CREATE PROCEDURE $tabela";
                        $sql = $con->query($sql);
                        $row = $sql->fetchAll(PDO::FETCH_ASSOC);
                        $create_table_banco = trim($row[0]['Create Procedure']);
                        $create_table_cru = $create_table_banco;
                        $create_table_banco = str_replace(' ', '', $create_table_banco);
                        $create_table_banco = str_replace("\r\n", '', $create_table_banco);
                        $create_table_banco = str_replace("\n", '', $create_table_banco);
                        if($show_queries){
                            $retorno_proc[$tabela]['banco'] = $create_table_cru;
                        }
                        //Comparador2 - Arquivo com create table da matriz
                        $file = $path.'sql/procedures/'.$tabela;
                        //caso exista o arquivo gerado
                        if(file_exists($file)){
                            $abrir_tabela = fopen($file, 'r');
                            $create_table_arquivo = '';
                            while(!feof($abrir_tabela)){
                                $create_table_arquivo .= fgets($abrir_tabela, 4096);
                            }
                        }
                        $create_table_cru = trim($create_table_arquivo);
                        @$create_table_arquivo = str_replace(' ', '', trim($create_table_arquivo));
                        $create_table_arquivo = str_replace(' ', '', $create_table_arquivo);
                        $create_table_arquivo = str_replace("\r\n", '', $create_table_arquivo);
                        $create_table_arquivo = str_replace("\n", '', $create_table_arquivo);
                        if($show_queries){
                            $retorno_proc[$tabela]['arquivo'] = $create_table_cru;
                        }
                        if($create_table_banco == $create_table_arquivo){
                            $retorno_proc[$tabela]['resultado'] = true;
                        }
                        else {
                            $retorno_proc[$tabela]['resultado'] = false;
                            $errors++;
                        }
                    }
                    else {
                        $retorno_proc[$tabela]['banco'] = 'Procedure Inexistente';
                        $retorno_proc[$tabela]['resultado'] = false;
                        $file = $path.'sql/procedures/'.$tabela;
                        //caso exista o arquivo gerado
                        if(file_exists($file)){
                            $abrir_tabela = fopen($file, 'r');
                            $create_table_arquivo = '';
                            while(!feof($abrir_tabela)){
                                $create_table_arquivo .= fgets($abrir_tabela, 4096);
                            }
                        }
                        $create_table_cru = trim($create_table_arquivo);
                        $retorno_proc[$tabela]['arquivo'] = $create_table_cru;
                        $errors++;
                    }

                }

            }
            fclose($abrir);
        }
        return Array('tabelas' => @$retorno, 'procedures' => @$retorno_proc, 'errors' => @$errors);
    }


    function BuildAllSql(){
        ini_set('display_errors', 'on');
        $path = $this->path;
        //echo "chmod 777 -R ".$path."sql/ \n";
        $con = $this->con;
        if(@is_dir($path.'sql/tables')){
            $files = glob($path.'sql/tables/*');
            foreach($files as $fl){
                unlink($fl);
            }
        }
        if(@is_dir($path.'sql/procedures')){
            $files = glob($path.'sql/procedures/*');
            foreach($files as $fl){
                unlink($fl);
            }
        }
        $arquivo_tabelas = 'tables.php';
        if(file_exists($path.'sql/'.$arquivo_tabelas)){
            $abrir = fopen($path.'sql/'.$arquivo_tabelas, 'r');
            while(!feof($abrir)){
                unset($create_table_arquivo);
                //Caso exista a linha da tabela ja com o trim aplicado
                $tabela = trim(fgets($abrir, 4096));
                if($tabela != ''){
                    $retorno[] = $this->BuildOneSql($tabela, 'tabela');
                }
            }
        }
        $arquivo_tabelas = 'procedures.php';
        if(file_exists($path.'sql/'.$arquivo_tabelas)){
            $abrir = fopen($path.'sql/'.$arquivo_tabelas, 'r');
            while(!feof($abrir)){
                unset($create_table_arquivo);
                //Caso exista a linha da tabela ja com o trim aplicado
                $tabela = trim(fgets($abrir, 4096));
                if($tabela != ''){
                    $retorno[] = $this->BuildOneSql($tabela, 'procedure');
                }
            }
        }
        return @$retorno;
    }

    function BuildOneSql($alvo, $tipo){
        $path = $this->path;
        $con = $this->con;
        if($tipo == 'tabela'){
            try {
                $sql = "SHOW CREATE TABLE $alvo";
                $sql = $con->query($sql);
                if($sql){
                    $row = $sql->fetchAll(PDO::FETCH_ASSOC);
                    $create_table_banco = $row[0]['Create Table'];
                    $create_table_banco = explode('AUTO_INCREMENT=', $create_table_banco);
                    $create_table_banco = $create_table_banco[0];
                    $create_table = $create_table_banco;
                    if(!file_exists($path.'sql/tables/')){
                        if(!mkdir($path.'sql/tables/')){
                            return 'Não há permissões de escrita em '.$path.'sql/';
                        }
                        chmod($path.'sql/tables/', 0777);
                    }
                    $file = $path.'sql/tables/'.$alvo;
                    $abrir = fopen($file, 'w+');
                    $escrever = fwrite($abrir, $create_table, strlen($create_table));
                    fclose($abrir);
                    chmod($file, 0777);
                }
            }
            catch(PDOException $e){
                throw new pdoDbException($e);
            }
            return $escrever;

        }
        if($tipo == 'procedure'){
            try {
                $sql = "SHOW CREATE PROCEDURE $alvo";
                $sql = $con->query($sql);
                if($sql){
                    $row = $sql->fetchAll(PDO::FETCH_ASSOC);
                    $create_table_banco = trim($row[0]['Create Procedure']);
                    $create_table = $create_table_banco;
                    if(!file_exists($path.'sql/procedures/')){
                        if(!mkdir($path.'sql/procedures/')){
                            return 'Não há permissões de escrita em '.$path.'sql/';
                        }
                        chmod($path.'sql/procedures/', 0777);
                    }
                    $file = $path.'sql/procedures/'.$alvo;
                    $abrir = fopen($file, 'w+');
                    $escrever = fwrite($abrir, $create_table, strlen($create_table));
                    fclose($abrir);
                    chmod($file, 0777);
                }
            }
            catch(PDOException $e){
                throw new pdoDbException($e);
            }
            return $escrever;

        }
    }
}

// Espaço para testes
// require_once '../general.inc.php';
// echo '<pre>';
// $PageConstructor = new PageConstructor(__DIR__ .'/../../../pages_new/3_administrativo/0_administrativo/02_cadastro_sistema/', $con);
//
// var_dump($PageConstructor->BuildAllSql());
// var_dump($PageConstructor->CheckSql());
// var_dump($PageConstructor->BuildOneSql('fla_front_controle', 'procedure'));
