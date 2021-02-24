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
    function BuscaTabelas($empresa, $representante = 0, $tabela = 0){
        $con = $this->con;
        if($representante == 0 AND $tabela == 0){
            $sql = "SELECT BusinessCadTabPreco.CdTabela as id_tabela,
            BusinessCadTabPreco.DsTabela as descricao_tabela
            FROM BusinessCadTabPreco
            WHERE BusinessCadTabPreco.cdEmpresa = :empresa";
            $sql = $con->prepare($sql);
            $sql->bindParam('empresa', $empresa);
        }
        elseif($representante != 0 && $tabela == 0){
            $sql = "SELECT a.CdTabela as id_tabela,
            a.DsTabela as descricao_tabela
            FROM BusinessCadTabPreco a
            LEFT JOIN BusinessCadPermiTabela b ON a.CdTabela=b.CdTabela
            WHERE a.cdEmpresa = :empresa AND
            b.CdRepresentante = :representante";
            $sql = $con->prepare($sql);
            $sql->bindParam('empresa', $empresa);
            $sql->bindParam('representante', $representante);

        }
        else {
            $sql = "SELECT a.CdTabela as id_tabela,
            a.DsTabela as descricao_tabela
            FROM BusinessCadTabPreco a
            LEFT JOIN BusinessCadPermiTabela b ON a.CdTabela=b.CdTabela
            WHERE a.cdEmpresa = :empresa AND
            b.CdRepresentante = :representante
            AND a.CdTabela = :tabela";
            $sql = $con->prepare($sql);
            $sql->bindParam('empresa', $empresa);
            $sql->bindParam('representante', $representante);
            $sql->bindParam('tabela', $tabela);

        }
        $sql->execute();
        $row = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }
    function BuscaCondicoesPagamento($empresa, $representante = 0){
        $con = $this->con;
        if($representante == 0){
            $sql = "SELECT a.CdCondPgto as id,
            a.DsCondPgto as descricao,
            a.Minimo as minimo
            FROM BusinessCadCondPgto a
            WHERE a.CdEmpresa = :empresa AND a.FlStatus='Ativo'";
            $sql = $con->prepare($sql);
            $sql->bindParam('empresa', $empresa);

        }
        else {
            $sql = "SELECT a.CdCondPgto as id,
            a.DsCondPgto as descricao,
            a.Minimo as minimo
            FROM BusinessCadCondPgto a
            LEFT JOIN BusinessCadPermiCondPgto b ON a.CdCondPgto=b.CdCondPgto
            WHERE a.CdEmpresa = :empresa AND
            b.CdRepresentante = :representante AND a.FlStatus='Ativo'";
            $sql = $con->prepare($sql);
            $sql->bindParam('empresa', $empresa);
            $sql->bindParam('representante', $representante);


        }
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
    function BuscaNaturezaOperacao($empresa, $representante = 0, $CdNatureza = 0){
        $con = $this->con;
        if($representante == 0){
            $sql = "SELECT BusinessCadNatOperacao.CdNatureza as id,
            BusinessCadNatOperacao.DsNatureza as descricao
            FROM BusinessCadNatOperacao";
            $sql = $con->prepare($sql);

        }
        elseif($representante != 0 && $CdNatureza != 0){
            $sql = "SELECT a.CdNatureza as id,
            a.DsNatureza as descricao
            FROM BusinessCadNatOperacao a
            LEFT JOIN BusinessCadPerminatureza b ON a.CdNatureza=b.CdNatureza

            WHERE b.CdRepresentante = :representante AND
            a.CdNatureza = :CdNatureza";
            $sql = $con->prepare($sql);
            $sql->bindParam('representante', $representante);
            $sql->bindParam('CdNatureza', $CdNatureza);

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
    function BuscaEmpresas($representante = 0){
        $con = $this->con;
        if($representante == 0){
            $sql = "SELECT BusinessCadEmpresa.FsEmpresa as nome,
             BusinessCadEmpresa.CdEmpresa as id
            FROM BusinessCadEmpresa ORDER BY BusinessCadEmpresa.FsEmpresa ASC";
            $sql = $con->prepare($sql);
        }
        else {
            $sql = "SELECT a.FsEmpresa as nome,
            a.CdEmpresa as id
            FROM BusinessCadEmpresa a
            INNER JOIN BusinessCadPermiCondPgto b ON a.CdEmpresa=b.CdEmpresa
            WHERE b.CdRepresentante = :representante
            GROUP BY a.CdEmpresa, a.FsEmpresa
            ORDER BY a.FsEmpresa ASC";
            $sql = $con->prepare($sql);
            $sql->bindParam('representante', $representante);
        }

        $sql->execute();
        $row = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }
    function VerificaAcessoMunicipio($municipio, $representante, $estado){
        $con = $this->con;
        $municipio = '%'.$municipio.'%';
        $estado = '%'.$estado.'%';

        $sql = "SELECT * FROM BusinessCadMunicipio a
        LEFT JOIN BusinessCadPermiMunicipio b ON a.CdMunicipio=b.Cdmunicipio

        WHERE b.CdRepresentante = :representante AND
        a.DsMunicipio LIKE :municipio
        AND a.Estado LIKE :estado ";
        $sql = $con->prepare($sql);

        $sql->bindParam('representante', $representante);
        $sql->bindParam('municipio', $municipio);
        $sql->bindParam('estado', $estado);


        $sql->execute();
        $row = $sql->fetchAll(PDO::FETCH_ASSOC);
        if(@$row){
            return 1;
        }
        else {
            return 0;
        }
    }

    function BuscaSegmentoMercado($representante, $CdSegmento = 0){
        $con = $this->con;
        if($representante != 0 && $CdSegmento == 0){
            $sql = "SELECT a.* FROM BusinessCadSegMercado a
            LEFT JOIN BusinessCadPermiSegMercado b ON a.CdSegmento=b.CdSegmento
            WHERE b.CdRepresentante = :representante";
            $sql = $con->prepare($sql);
            $sql->bindParam('representante', $representante);
        }
        elseif($representante != 0 && $CdSegmento != 0){
            $sql = "SELECT a.* FROM BusinessCadSegMercado a
            LEFT JOIN BusinessCadPermiSegMercado b ON a.CdSegmento=b.CdSegmento
            WHERE b.CdRepresentante = :representante AND a.CdSegmento = :CdSegmento";
            $sql = $con->prepare($sql);
            $sql->bindParam('representante', $representante);
            $sql->bindParam('CdSegmento', $CdSegmento);

        }
        else {
            $sql = "SELECT a.* FROM BusinessCadSegMercado a ";
            $sql = $con->prepare($sql);
        }
        $sql->execute();
        $row = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }
    function BuscaRegiao($representante, $CdRegiao = 0){
        $con = $this->con;
        if($representante != 0 && $CdRegiao == 0){
            $sql = "SELECT a.* FROM BusinessCadRegiao a
            LEFT JOIN BusinessPermiRegiao b ON a.CdRegiao=b.CdRegiao
            WHERE b.CdRepresentante = :representante";
            $sql = $con->prepare($sql);
            $sql->bindParam('representante', $representante);
        }
        elseif($representante != 0 && $CdRegiao != 0){
            $sql = "SELECT a.* FROM BusinessCadRegiao a
            LEFT JOIN BusinessPermiRegiao b ON a.CdRegiao=b.CdRegiao
            WHERE b.CdRepresentante = :representante AND a.CdRegiao = :CdRegiao";
            $sql = $con->prepare($sql);
            $sql->bindParam('representante', $representante);
            $sql->bindParam('CdRegiao', $CdRegiao);

        }
        else {
            $sql = "SELECT a.* FROM BusinessCadRegiao a ";
            $sql = $con->prepare($sql);
        }
        $sql->execute();
        $row = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }
}
