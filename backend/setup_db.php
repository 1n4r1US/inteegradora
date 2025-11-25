<?php
require_once 'config/config.php';

echo "<h1>Actualización de Base de Datos</h1>";

// 1. Verificar conexión
if ($conn->connect_error) {
    die("<p style='color:red'>Error de conexión: " . $conn->connect_error . "</p>");
}
echo "<p style='color:green'>Conexión exitosa.</p>";

// 2. Verificar si la columna 'modalidad' existe en la tabla 'psicologo'
$checkColumn = $conn->query("SHOW COLUMNS FROM psicologo LIKE 'modalidad'");

if ($checkColumn && $checkColumn->num_rows == 0) {
    echo "<p>La columna 'modalidad' no existe. Intentando agregarla...</p>";
    $sql = "ALTER TABLE psicologo ADD COLUMN modalidad ENUM('presencial', 'virtual') DEFAULT 'presencial'";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green'>Columna 'modalidad' agregada correctamente.</p>";
    } else {
        echo "<p style='color:red'>Error al agregar columna: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color:blue'>La columna 'modalidad' ya existe. No es necesario hacer cambios.</p>";
}

echo "<hr>";
echo "<p>Base de datos lista. Puedes cerrar esta pestaña.</p>";
$conn->close();
?>