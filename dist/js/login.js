function unSetSession(){
    var dados;
    $.ajax({
        type: 'POST',
        url: 'dist/php/controllers/sessionControl.api.php',
        data: {'action': 'unsetSession'},
        async: false,
        dataType: 'json',
        success: function (data)
        {
           dados = data;
        }
    });
    return dados;
}

function Login(){
    $('#login-btn').prop('disabled', 1);
    $('#login-btn').val('Aguarde, realizando login...');
    var user = $('#user').val();
    var pass = $('#pass').val();
    if(user == '' || pass == ''){
       $('#login-btn').prop('disabled', 0);
       $('#login-btn').val('Log In');
       toastr.warning('Preencha nome de usuario e senha');
    }
    else {
        $.ajax({
            type: 'POST',
            url: 'dist/php/controllers/login.api.php',
            data: {'user': user, 'pass': pass},
            dataType: 'json',
            async: true,
            success: function (response)
            {
                if(response['tipo'] == 0){
                    $('#login-btn').prop('disabled', 0);
                    $('#login-btn').val('Log In');
                    toastr.error(response['texto']);
                }
                if(response['tipo'] == 1){
                    toastr.success(response['texto']);
                    setTimeout(function() {
                        window.location.href = "index.html";
                    }, 2000);
                }
            }
        });
    }
}
$(document).on("submit", "form", function(e){
    e.preventDefault();
    return  false;
});

$(document).ready(function(){
    unSetSession();
})
