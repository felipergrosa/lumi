<?php
// $con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");

class SqlServer {
    public $con;
    function __construct($con){
        $this->con = $con;
    }

    function BuscaProduto($busca, $tabela, $empresa = null, $por_id = false){
        $con = $this->con;
        if($por_id){
            $sql_search = " AND BusinessCadProduto.CdProduto = '$busca' ";
        }
        else {
            $sql_search = " AND BusinessCadProduto.DsProduto LIKE '%$busca%' ";
        }
        $sql = "SELECT TOP 100 BusinessCadProduto.DsProduto as produto_descricao,
                BusinessCadProduto.CdProduto as produto_id,
                BusinessCadTabPrecoItem.Venda as produto_preco,
                BusinessCadTabPrecoItem.CdTabela as tb_id


                FROM BusinessCadProduto
                LEFT JOIN BusinessCadTabPrecoItem ON BusinessCadProduto.CdProduto = BusinessCadTabPrecoItem.CdProduto
                WHERE BusinessCadTabPrecoItem.CdTabela = $tabela
                $sql_search";

        $sql = $con->prepare($sql);
        $sql->execute();
        $row = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }
    function BuscaTabelas($empresa){
        $con = $this->con;
        $sql = "SELECT BusinessCadTabPreco.CdTabela as id_tabela,
        BusinessCadTabPreco.DsTabela as descricao_tabela
        FROM BusinessCadTabPreco
        WHERE BusinessCadTabPreco.cdEmpresa = $empresa";
        $sql = $con->prepare($sql);
        $sql->execute();
        $row = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }
    function BuscaEmpresas(){
        $con = $this->con;
        $sql = "SELECT BusinessCadEmpresa.FsEmpresa as nome,
         BusinessCadEmpresa.CdEmpresa as id
        FROM BusinessCadEmpresa ORDER BY BusinessCadEmpresa.FsEmpresa ASC";
        $sql = $con->prepare($sql);
        $sql->execute();
        $row = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }
}
