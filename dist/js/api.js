url_global = 'http://192.168.16.101/projects/sig4/';
function systemInfo(){
    var dados;
    $.ajax({
        type: 'POST',
        url: url_global + 'dist/php/api/systemInfo.api.php',
        async: false,
        dataType: 'json',
        success: function (data)
        {
           dados = data;
        }
    });
    return dados;
}
function checkSession(){
    var dados;
    $.ajax({
        type: 'POST',
        url: url_global + 'dist/php/api/sessionControl.api.php',
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
function unsetSession(){
    var dados;
    $.ajax({
        type: 'POST',
        url: url_global + 'dist/php/api/sessionControl.api.php',
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
function getUserData(){
    var dados;
    $.ajax({
        type: 'POST',
        url: url_global + 'dist/php/api/sessionControl.api.php',
        data: {'action': 'getUserData'},
        async: false,
        dataType: 'json',
        success: function (data)
        {
           dados = data;
        }
    });
    return dados;
}
function exibeMenuEsquerda(){
    $.ajax({
        type: 'POST',
        url: url_global + 'dist/php/api/sessionControl.api.php',
        data: {'action': 'exibeMenuEsquerda'},
        async: false,
        success: function (data)
        {
           dados = data;
        }
    });
    return dados;
}
