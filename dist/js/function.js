IntervalMaster = '';
function ShowLoading(){
    $("#loading_animation").fadeIn(500);
    $("#conteudo_dinamico").addClass('blur_loading');
}
function HideLoading(){
    $("#loading_animation").fadeOut(500);
    $("#conteudo_dinamico").removeClass('blur_loading');
}
function CarregaPagina(pagina) {
    console.log(pagina);
    ShowLoading();
    clearInterval(IntervalMaster);
    $.ajax({
        type: 'POST',
        dataType: 'html',
        url: pagina,
        async: true,
        success: function (response) {
            if(response == 'SEMPERMISSAO'){
                toastr.error('Você não possui privilégios para ver esta tela.');
                $('#conteudo_dinamico').load('index_conteudo.html');
            }
            else {
                $('#conteudo_dinamico').html(response);
            }

            /* $('#conteudo_dinamico div').each(function () {
                $(this).addClass('animated fadeInRightBig');
            }); */
        },
        error: function (response) {
            toastr.error('Arquivo não encontrado.');
            CarregaPagina('content/components/index_conteudo.html');
             HideLoading();

        },
        complete: function () {
            HideLoading();
            // $.smkProgressBar({
            //     element: '#conteudo_dinamico',
            //     status: 'end',
            //     barColor: '#000'
            // });
        }
    });
    $('.app-container').removeClass('sidebar-mobile-open');
    $('.hamburger').removeClass('is-active');
}
function RandomCssEffect() {
    var effect = ['animated fadeInLeftBig', 'animated fadeInLeftBig', 'animated fadeInRightBig', 'animated fadeInRightBig', 'animated fadeInDownBig', 'animated fadeInDownBig', 'animated fadeInDownBig', 'animated fadeInDownBig', 'animated fadeInUpBig', 'animated zoomIn', 'animated zoomIn'];
    var choise = Math.floor((Math.random() * 10) + 1);
    return(effect[choise]);
}

function moeda(z) {
    v = z.value;
    v = v.replace(/\D/g, "")  //permite digitar apenas números
    v = v.replace(/[0-9]{12}/, "inválido")   //limita pra máximo 999.999.999,99
    v = v.replace(/(\d{1})(\d{8})$/, "$1.$2")  //coloca ponto antes dos ultimos 8 digitos
    v = v.replace(/(\d{1})(\d{5})$/, "$1.$2")  //coloca ponto antes dos ultimos 5 digitos
    v = v.replace(/(\d{1})(\d{1,2})$/, "$1,$2")        //coloca virgula antes dos ultimos 2 digitos
    z.value = v;
}
function CarregaModal(titulo, conteudo, tipo = 'texto', post = false, backdrop = true, parameters = false) {
    if (tipo == 'texto') {
        $('#modal_general_titulo').html('');
        $('#modal_general_body').html('');
        $('#modal_general_titulo').html(titulo);
        $('#modal_general_body').html(conteudo);
    }
    if (tipo == 'load') {
        $('#modal_general_titulo').html('');
        $('#modal_general_body').html('');
        $('#modal_general_titulo').html(titulo);
        $('#modal_general_body').load(conteudo);
    }
    if (tipo == 'post') {
        $.ajax({
            type: 'POST',
            url: conteudo,
            data: parameters,
            success: function (data)
            {
                $('#modal_general_titulo').html('');
                $('#modal_general_body').html('');
                $('#modal_general_titulo').html(titulo);
                $('#modal_general_body').html(data);
            }
        });
    }
    if(backdrop){
        // $('#modal_general').modal({backdrop:'static'});
        $('#modal_abrir').click();

    }
    else {
        $('#modal_abrir').click();
    }
}
function SomenteNumeros(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}
function GeneralControl(first = false){
    $.ajax({
            type: 'POST',
            url: 'dist/php/php_checks/GeneralCheck.php',
            data: {'tipo':first},
            success: function (data)
            {
                if(data != 0){
                    $.smkAlert({text: data, type: 'info'});
                }
            }
        });
}

function moedamatriz(z) {
    v = z;
    v = v.replace(/\D/g, "")  //permite digitar apenas números
    v = v.replace(/[0-9]{12}/, "inválido")   //limita pra máximo 999.999.999,99
    v = v.replace(/(\d{1})(\d{8})$/, "$1.$2")  //coloca ponto antes dos ultimos 8 digitos
    v = v.replace(/(\d{1})(\d{5})$/, "$1.$2")  //coloca ponto antes dos ultimos 5 digitos
    v = v.replace(/(\d{1})(\d{1,2})$/, "$1,$2")        //coloca virgula antes dos ultimos 2 digitos
    return v;
}
function CadProdCalculaPreco(custo, campopreco, campomargem) {
    var custo = $('#' + custo).val();
    custo = custo.replace('.', '');
    custo = custo.replace(',', '.');
    var margem = $('#' + campomargem).val();
    margem = margem.replace('.', '');
    margem = margem.replace(',', '.');
    var soma1 = 0;
    soma1 = custo / 100 * margem;
    var final = 0;
    final = (parseFloat(soma1) + parseFloat(custo));
    final = Number(final);
    final = (final).toFixed(2);
    final = moedamatriz(final);
    $('#' + campopreco).val(final);
}
function CadProdCalculaMargem(custo, campopreco, campomargem) {
    var custo = $('#' + custo).val();
    custo = custo.replace('.', '');
    custo = custo.replace(',', '.');
    var preco = $('#' + campopreco).val();
    preco = preco.replace('.', '');
    preco = preco.replace(',', '.');
    var soma1 = 0;
    soma1 = parseFloat(preco) * 100 / parseFloat(custo) - 100;
    soma1 = Number(soma1);
    soma1 = (soma1).toFixed(2);
    soma1 = moedamatriz(soma1);
    $('#' + campomargem).val(soma1);
}
// var GeneralTime = setInterval(GeneralControl, 4000);
$(document).ready(function(){
    /* GeneralControl('firstcheck'); */
});
function percent(z){
    v = z.value;
    if(v.length == 1){
        v = v.replace(/\D/g, "");
    }
    if(v != ""){

        v = v.match(/^\d+\,?\d*/)[0];
        z.value = v;
    }
    else {
        z.value = v;
    }
}
function percentPoint(z){
    v = z.value;
    if(v.length == 1){
        v = v.replace(/\D/g, "");
    }
    if(v != ""){

        v = v.match(/^\d+\.?\d*/)[0];
        z.value = v;
    }
    else {
        z.value = v;
    }
}
function apenasNumeros(z){
    v = z.value;
    if(v.length == 1){
        v = v.replace(/\D/g, "");
    }
    if(v != ""){

        v = v.replace(/\D/g, "");
        z.value = v;
    }
    else {
        z.value = v;
    }
}
