$(function () {

    const inputDate = $("#fecha")

    const hoy = new Date().toISOString().split("T")[0]

    inputDate.attr("min", hoy)

    const obj = JSON.parse(sessionStorage.getItem("user"))
    if (obj != false) {
        const letra = obj.nombre[0]
        $("#modal_perfil h1").html(letra.toUpperCase())
        $("#modal_perfil p.nombre").html(obj.nombre).css({ "padding": "10px" })
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

        reservarAula(obj_enviar)

    })

    $(".btn-verReservas").on('click', function () {
        mostrarFechas()
    })

    // al cerrar sesión primero borrar toda la información del 
    // login de usuario y después redirigir a la página principal

    $(".btn-cerrarSesion").on('click', function () {
        sessionStorage.clear()
        location.href = "../index.html"
    })



    $(".btn-filtrar").on('click', function () {
        const padre = $("#filtros")
        const hora = padre.find("select#hora").val()
        const fecha = padre.find("#fecha_filtro").val()
        const fecha_reservada = fecha
        const hora_reservada = hora
        if (fecha != "") {
            filtrarReservas({
                fecha_reservada,
                hora_reservada
            })
        } else {
            alert("Debes seleccionar una fecha para poder filtrar")
        }
    })

})

function reservarAula(obj = {}) {
    console.log("se le ha enviado", obj);
    $.ajax({
        url: "../php/reservar.php",
        method: "POST",
        data: obj,
        dataType: "json",
        success: function (response) {
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
    for (const valor of array) {
        $(valor).find("span").removeClass("reservado").html("libre")
        $(valor).find("button").removeClass("reservado").html("Reservar").attr("disabled", false)
    }
}

function mostrarFechas() {
    $.ajax({
        url: "../php/mostrarReservas.php",
        method: "POST",
        dataType: "json",
        success: function (response) {
            const array = response
            let cadena = ""
            $("#resultado").empty()
            array.forEach(e => {
                const objeto = {
                    nombre: e.nombres,
                    fecha: formatDate(e.fecha),
                    hora: e.hora + "º"
                }
                cadena += tarjetaReservas(objeto)
            });
            $("#resultado").append(cadena)
        },
        error: function (error) {
            console.log(error)
        }
    })
}

function formatDate(fecha = "") {
    const date = fecha.split("-")
    const año = date[0]
    const mes = date[1]
    const dia = date[2]

    return `${dia}/${mes}/${año}`
}

function tarjetaReservas(obj = {}) {
    return `
    <div>
        <p>${obj.nombre}</p>
        <p>${obj.fecha}</p>
        <p>${obj.hora}</p>
    </div>`
}

function filtrarReservas(obj = {}) {
    $.ajax({
        url: "../php/filtrarFechas.php",
        data: obj,
        method: "POST",
        dataType: "json",
        success: function (response) {
            console.log(response);
            const array = response
            $("#resultado").empty()
            let cadena = ""
            array.forEach(e => {
                const objeto = {
                    nombre: e.nombre,
                    fecha: formatDate(e.fecha),
                    hora: e.hora + "º"
                }
                cadena += tarjetaReservas(objeto)
            });
            $("#resultado").append(cadena)
        },
        error: function (err) {
            console.log(err);
        }
    })
}