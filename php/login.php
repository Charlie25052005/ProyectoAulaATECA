<?php

header('Content-Type: application/json; charset=utf-8');

try {

    $pdo = new PDO(
        'mysql:host=localhost;dbname=tu_base_de_datos;charset=utf8mb4',
        'tu_usuario',
        'tu_contraseña'
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $correo = $_POST['correo'] ?? '';
    $contrasena = $_POST['passwd'] ?? '';

    if (empty($correo) || empty($contrasena)) {
        echo json_encode([
            'success' => false,
            'error' => 'Faltan credenciales'
        ]);
        exit;
    }

    $stmt = $pdo->prepare(
        'SELECT ID_usuario, Nombre, Apellido1, Apellido2, Correo, Contraseña
            ROM usuarios
            WHERE Correo = ?
            LIMIT 1'
    );

    $stmt->execute([$correo]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && $contrasena === $usuario['Contraseña']) {

        unset($usuario['Contraseña']);

        echo json_encode([
            'success' => true,
            'user' => $usuario
        ]);

    } else {

        echo json_encode([
            'success' => false,
            'error' => 'Credenciales incorrectas'
        ]);

    }

} catch (PDOException $e) {

    echo json_encode([
        'success' => false,
        'error' => 'Error de conexión a la base de datos'
    ]);

}