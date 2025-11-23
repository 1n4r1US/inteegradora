<?php
require_once __DIR__ . '/../config/config.php';

// Datos del usuario de prueba
$nombre = 'Juan';
$apellido = 'Pérez';
$correo = 'juan@ejemplo.com';
$telefono = '5551234567';
$direccion = 'Calle Falsa 123';
$password = password_hash('123456', PASSWORD_DEFAULT); // Contraseña: 123456
$rol = 'paciente';

// Insertar en la tabla usuarios
$sql = "INSERT INTO usuarios (nombre, apellido, correo, telefono, direccion, password, rol)
        VALUES ('$nombre', '$apellido', '$correo', '$telefono', '$direccion', '$password', '$rol')";

if ($conn->query($sql) === TRUE) {
    echo 'Usuario insertado correctamente.';
} else {
    echo 'Error: ' . $conn->error;
}

$conn->close();
?>