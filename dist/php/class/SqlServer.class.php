<?php
// $con_sql_server = new PDO ("dblib:host=$mssql_hostname;dbname=$mssql_dbname", "$mssql_username", "$mssql_pw");

class SqlServer {
    public $con;
    function __construct($con){
        $this->con = $con;
    }

    function BuscaProduto($busca, $tabela, $por_id = false, $limit = 100, $empresa = null){
        $con = $this->con;
        if($por_id){
            $sql_search = " AND BusinessCadProduto.CdProduto = '$busca' ";
        }
        else {
            $sql_search = " AND BusinessCadProduto.DsProduto LIKE '%$busca%' ";
        }
        $sql = "SELECT TOP $limit BusinessCadProduto.DsProduto as produto_descricao,
                BusinessCadProduto.CdEmpresa as empresa,
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
    function BuscaCliente($busca, $empresa, $representante, $por_id = false, $limit = 100){
        $con = $this->con;
        if($por_id){
            $sql_search = " AND BusinessCadProduto.CdProduto = '$busca' ";
        }
        else {
            $sql_search = " AND BusinessCadProduto.DsProduto LIKE '%$busca%' ";
        }

        if($empresa != 0){
            $sql = "SELECT TOP 5 a.Cnpj_Cnpf, a.FsCliente FROM BusinessCadCliente a
            LEFT JOIN BusinessCadClienteLC b ON a.Cnpj_Cnpf=b.Cnpj_Cnpf
            WHERE (a.Cnpj_Cnpf = :busca OR a.FsCliente LIKE :buscalike OR a.RzCliente LIKE :buscalike) AND b.CdEmpresa = :empresa AND a.CdRepresentante = :representante
            GROUP BY a.Cnpj_Cnpf, a.FsCliente
            ";
            $buscalike = '%'.$busca.'%';
            $sql = $con->prepare($sql);
            $sql->bindParam('busca', $busca);
            $sql->bindParam('buscalike', $buscalike);
            $sql->bindParam('empresa', $empresa);
            $sql->bindParam('representante', $representante);
        }
        else {
            $sql = "SELECT TOP 5 a.Cnpj_Cnpf, a.FsCliente FROM BusinessCadCliente a
            WHERE (a.Cnpj_Cnpf = :busca OR a.FsCliente LIKE :buscalike OR a.RzCliente LIKE :buscalike)
            GROUP BY a.Cnpj_Cnpf, a.FsCliente
            ";
            $buscalike = '%'.$busca.'%';
            $sql = $con->prepare($sql);
            $sql->bindParam('busca', $busca);
            $sql->bindParam('buscalike', $buscalike);
            $sql->bindParam('representante', $representante);
        }

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
    function BuscaCondicoesPagamento($empresa){
        $con = $this->con;
        $sql = "SELECT BusinessCadCondPgto.CdCondPgto as id,
        BusinessCadCondPgto.DsCondPgto as descricao,
        BusinessCadCondPgto.Minimo as minimo
        FROM BusinessCadCondPgto
        WHERE BusinessCadCondPgto.CdEmpresa = $empresa";
        $sql = $con->prepare($sql);
        $sql->execute();
        $row = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }
    function BuscaTransportadoras($empresa){
        $con = $this->con;
        $sql = "SELECT BusinessCadTransportadora.CdTransportadora as id,
        BusinessCadTransportadora.FsTransportadora as descricao
        FROM BusinessCadTransportadora";
        $sql = $con->prepare($sql);
        $sql->execute();
        $row = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }
    function BuscaNaturezaOperacao($empresa, $representante = 0){
        $con = $this->con;
        if($representante == 0){
            $sql = "SELECT BusinessCadNatOperacao.CdNatureza as id,
            BusinessCadNatOperacao.DsNatureza as descricao
            FROM BusinessCadNatOperacao";
            $sql = $con->prepare($sql);

        }
        else {
            $sql = "SELECT BusinessCadNatOperacao.CdNatureza as a.id,
            BusinessCadNatOperacao.DsNatureza as a.descricao
            FROM BusinessCadNatOperacao a
            LEFT JOIN BusinessCadPerminatureza ON a.CdNatureza=b.cd

            WHERE b.CdRepresentante = :representante";
            $sql = $con->prepare($sql);
            $sql->bindParam('representante', $representante);

        }
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
    function VerificaAcessoMunicipio($municipio, $representante){
        $con = $this->con;
        $municipio = '%'.$municipio.'%';

        $sql = "SELECT * FROM BusinessCadMunicipio a
        LEFT JOIN BusinessCadPermiMunicipio b ON a.CdMunicipio=b.Cdmunicipio

        WHERE b.CdRepresentante = :representante AND
        a.DsMunicipio LIKE :municipio";
        $sql = $con->prepare($sql);

        $sql->bindParam('representante', $representante);
        $sql->bindParam('municipio', $municipio);

        $sql->execute();
        $row = $sql->fetchAll(PDO::FETCH_ASSOC);
        if(@$row){
            return 1;
        }
        else {
            return 0;
        }
    }
}
