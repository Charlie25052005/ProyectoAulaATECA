$(function () {

    const inputDate = $("#fecha")

    const hoy = new Date().toISOString().split("T")[0]

    inputDate.attr("min", hoy)

    const obj = JSON.parse(sessionStorage.getItem("user"))
    if (obj != false) {
        const letra = obj.nombre[0]
        $("#modal_perfil h1").html(letra.toUpperCase())
    }

    $("#fecha").on('change', function () {
        const obj = {
            fecha: $(this).val()
        }
        limpiarTabla()
        verHoraDeFechaReservada(obj)
    })



    $(document).on('click', '.btn-reservar', function () {
        let obj = ""
        if (sessionStorage.getItem("user") != null) {
            obj = JSON.parse(sessionStorage.getItem("user"))
        }

        const fecha_reservada = $("#fecha").val()
        const hora_reservada = $(this).val()
        const id_usuario = parseInt(obj.id)
        const id_curso = 1

        if (fecha_reservada == '') {
            alert("Debes seleccionar una fecha")
            return
        }

        const obj_enviar = {
            fecha_reservada: fecha_reservada,
            hora_reservada: hora_reservada,
            id_usuario: id_usuario,
            id_curso: id_curso
        }

        console.log(obj_enviar);

        reservarAula(obj_enviar)

    })

    $(".btn-verReservas").on('click', function () {
        let cadena = ""
        if (localStorage.length > 0) {


            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i)
                const array = JSON.parse(localStorage.getItem(key)) != null
                    ? JSON.parse(localStorage.getItem(key))
                    : []
                for (const valor of array) {
                    cadena += tarjetaReservas(valor)
                }
            }
            $("#resultado").empty()
            $("#resultado").html(`
            <div>
                <p>Nombre de usuario</p>
                <p>Fecha Reservada</p>
                <p>Hora reservada</p>
            </div>` )
            $("#resultado").append(cadena)
        }
    })

    // al cerrar sesión primero borrar toda la información del 
    // login de usuario y después redirigir a la página principal

    $(".btn-cerrarSesion").on('click', function () {
        sessionStorage.clear()
        location.href = "../index.html"
    })


})

function tarjetaReservas(obj) {
    return `
    <div>
        <p>Usuario - X</p>
        <p>${obj.fecha}</p>
        <p>${obj.hora}</p>
    </div>`
}

function reservarAula(obj = {}) {
    $.ajax({
        url: "../php/reservar.php",
        method: "POST",
        data: obj,
        dataType: "json",
        success: function (response) {
            console.log(response);
            if (response.success == true) {
                const hora = obj.hora_reservada
                const elemento = $(`table tr:nth-child(${hora})`)
                elemento.find("span").html("Reservado").addClass("reservado")
                elemento.find("button").html("Reservado").addClass("reservado").attr("disabled", true)
                alert(response.message)
            }
        },
        error: function (error) {
            console.log(error);
        }
    })
}

function verHoraDeFechaReservada(obj = { fecha: "" }) {
    $.ajax({
        url: "../php/verFechasReservadas.php",
        method: "POST",
        data: obj,
        success: function (respuesta) {
            console.log(respuesta);
            const array = respuesta
            if (array.length > 0) {
                array.forEach(e => {
                    const hora = parseInt(e.hora)
                    const elemento = $(`table tr:nth-child(${hora})`)
                    console.log(elemento);
                    elemento.find("span").addClass("reservado").html("Reservado")
                    elemento.find("button").addClass("reservado").html("Reservado").attr("disabled", true)
                });
            }
        },
        error: function (error) {
            console.log(error);
        }
    })
}

function limpiarTabla() {
    const array = $("table tr")
    for ( const valor of array ) {
        $(valor).find("span").removeClass("reservado").html("libre")
        $(valor).find("button").removeClass("reservado").html("Reservar").attr("disabled",false)
    }
}