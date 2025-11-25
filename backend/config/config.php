<?php
// Configuración general del proyecto
// backend/config/config.php

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'psicologia';

try {
    $conn = new mysqli($host, $user, $password, $database);

    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }

    $conn->set_charset('utf8');

} catch (Exception $e) {
    // En producción, registrar el error en un log y no mostrarlo al usuario
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión con la base de datos.']);
    exit;
}
