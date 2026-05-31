$(function () {

    const inputDate = $("#fecha")

    const hoy = new Date().toISOString().split("T")[0]

    inputDate.attr("min", hoy)


    reservar()

    $("#fecha").on('change', function () {
        $("table tr").each((indice, elemento) => {
            $(elemento).find("span").removeClass("reservado").html("Libre")
            $(elemento).find("button").removeClass("reservado").html("Reservar").attr("disabled", false)
        })
        reservar()
    })

    $(document).on('click', '.btn-reservar', function () {
        const fecha = $("#fecha").val()

        if (!fecha) {
            alert("Debes elegir una fecha")
            mostrarDecoradoElTexto("Error fecha vacia: Debes elegir una fecha")
            return
        }

        const hora = $(this).val()
        const elemento = $(this).closest(`table tr:nth-child( ${$(this).val()} )`)
        elemento.find("span").addClass("reservado").html("Reservado")
        $(this).html("Reservado").addClass("reservado").attr("disabled", true)


        const obj = { hora:hora, fecha:fecha }
        const arrayReservas = JSON.parse(localStorage.getItem(fecha)) ?? []

        arrayReservas.push(obj)
        localStorage.setItem( fecha,JSON.stringify(arrayReservas) )

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
        localStorage.clear()
        location.href = "../index.html"
    })

})

function reservar() {
    const array = JSON.parse(localStorage.getItem($("#fecha").val()))

    if (array != null) {
        array.forEach(e => {
            const elemento = $(`table tr:nth-child( ${e.hora} )`)
            elemento.find("button").attr("disabled", true).addClass("reservado").html("Reservado")
            elemento.find("span").addClass("reservado").html("Reservado")
        })
    }
}

function mostrarDecoradoElTexto(texto = "") {
    console.log(`%c${texto.toUpperCase()}`, "color: #fff ; background-color: tomato ; width: 100% ; padding: 10px ; display:block; text-align: center ; font-size: 30pt ; font-family: 'Impact, Haettenschweiler, Arial Narrow Bold, sans-serif ' ; ");
}

function tarjetaReservas(obj) {
    return `
    <div>
        <p>Usuario - X</p>
        <p>${obj.fecha}</p>
        <p>${obj.hora}</p>
    </div>`
}

function reservarAula(obj) {}