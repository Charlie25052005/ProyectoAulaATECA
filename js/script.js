$(function () {
    $('.boton-registrar').on('click', function () {
        const modalLogin = $(this).closest("#modal")
        const modalRegistrar = $("#modalRegistrar")
        console.log(modal);
        modalLogin[0].hidePopover()
        modalRegistrar[0].showPopover()
    })

    $(".btn-login").on('click',function(){
        location.href = "../webs/reservas.html"
    })

})