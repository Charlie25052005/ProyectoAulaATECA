$(function () {

    $(".btn-login").on('click', function () {
        const passwd = $("#passwd").val()
        const correo = $("#correo").val()

        alert(passwd + " " + correo)
        // location.href = "webs/reservas.html"
        login({
            passwd:passwd,
            email:correo,
        })
    })

})

function login(obj = {}) {
    $.ajax({
        url: "php/login.php",
        method: "POST",
        data: obj,
        dataType: "json",
        success: function (respuesta) {
            console.log(respuesta);
        },
        error: function (error) {
            console.log(error);
        }
    })
}