function checkSession(){
    var dados;
    $.ajax({
        type: 'POST',
        url: 'dist/php/controllers/sessionControl.api.php',
        data: {'action': 'checkSession'},
        async: false,
        dataType: 'json',
        success: function (data)
        {
           dados = data;
        }
    });
    return dados;
}


$(document).ready(function(){
    $('#sidebar').load('content/components/sidebar_menu.php');
    $('#info_usuario').load('content/components/info_usuario.php');
    CarregaPagina('content/components/index_conteudo.html');
    if(checkSession() == false){

        location.href='login.html';
    }
});
