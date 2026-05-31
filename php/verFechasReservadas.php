<?php
$host = 'localhost'; // Cambia esto si es necesario
$user = 'root';
$password = '';
$database = 'MiBaseDeDatos';

// Conectar a la base de datos
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener la fecha desde la petición POST
$fechaSeleccionada = isset($_POST['fecha']) ? $_POST['fecha'] : '';

if ($fechaSeleccionada) {
    // Preparar la consulta SQL
    $sql = "SELECT Fecha_reservada as 'fecha', Hora_reservada as 'hora' , id_usuario as 'id_user' , id_curso FROM Reserva WHERE Fecha_reservada = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $fechaSeleccionada); // 's' indica que es un string

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener los resultados
    $result = $stmt->get_result();
    $reservas = array();

    // Recorrer los resultados
    while ($row = $result->fetch_assoc()) {
        $reservas[] = $row; // Añadir cada fila al array
    }

    // Devolver los datos como JSON
    header('Content-Type: application/json');
    echo json_encode($reservas);

    // Cierre de la declaración y conexión
    $stmt->close();
} else {
    echo json_encode(array("error" => "La fecha no existe")); // Manejo de error
}

$conn->close();
