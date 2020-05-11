<?php

class Enderecos {
    private $con;
    function __construct($con){
        $this->con = $con;
    }
    function novoEndereco($campos){
        $campos['cep'] = str_replace('-', '', $campos['cep']);
        $con = $this->con;
        $sql = "SELECT * FROM dados_enderecos WHERE
        cep = :cep && numero <=> :numero && endereco <=> :endereco && complemento <=> :complemento";
        $sql = $con->prepare($sql);
        $sql->bindParam('cep', $campos['cep']);
        $sql->bindParam('numero', $campos['numero']);
        $sql->bindParam('endereco', $campos['endereco']);
        $sql->bindParam('complemento', $campos['complemento']);
        $sql->execute();
        if($sql->rowCount() > 0){
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            return $row['id'];
            exit;
        }
        else {
            if(@!isset($campos['complemento'])){
                $campos['complemento'] = null;
            }
            $sql = "INSERT INTO dados_enderecos
            (cep, endereco, complemento, bairro, cidade, estado, numero)
            VALUES
            (:cep, :endereco, :complemento, :bairro, :cidade, :estado, :numero)";
            $sql = $con->prepare($sql);
            $sql->bindParam('cep', $campos['cep']);
            $sql->bindParam('endereco', $campos['endereco']);
            $sql->bindParam('complemento', $campos['complemento']);
            $sql->bindParam('bairro', $campos['bairro']);
            $sql->bindParam('cidade', $campos['cidade']);
            $sql->bindParam('estado', $campos['estado']);
            $sql->bindParam('numero', $campos['numero']);

            $sql->execute();

            return $con->lastInsertId();

        }


    }

    function echo($val){
        return $val;
    }
}
