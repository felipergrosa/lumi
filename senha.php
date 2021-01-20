<?php

// function fSenhaCript(Senha)
//     dim xx
//     aux=""
//
//     for xx=1 to len(senha)
//         aux=aux & chr(asc(mid(senha,xx,1))+xx+17)
//     next
//
//     fSenhaCript=aux
// end function


$senha = "6022015";
function CodificaSenhaNobre($senha){
    $senha = mb_strtoupper(trim($senha));
    $saida = "";
    for($i=0; $i<=strlen($senha); $i++){
        $saida .= chr(ord(substr($senha, $i, 1))+$i+18);
    }
    return $saida;
}
