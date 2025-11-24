<?php
/**
 * Archivo de configuración de la base de datos
 * 
 * Define las constantes de conexión a la base de datos MySQL
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'psicologia');
define('DB_CHARSET', 'utf8mb4');

// Configuración de la zona horaria
date_default_timezone_set('America/Mexico_City');

// Habilitar reporte de errores en desarrollo (comentar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Función para obtener la conexión a la base de datos
 * 
 * @return mysqli|false Retorna el objeto de conexión o false en caso de error
 */
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        error_log("Error de conexión a la base de datos: " . $conn->connect_error);
        return false;
    }
    
    // Establecer el charset
    $conn->set_charset(DB_CHARSET);
    
    return $conn;
}

/**
 * Función para cerrar la conexión a la base de datos
 * 
 * @param mysqli $conn Objeto de conexión a cerrar
 */
function closeDBConnection($conn) {
    if ($conn && !$conn->connect_error) {
        $conn->close();
    }
}

// Configuración de CORS (si es necesario para la API)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=UTF-8');

// Manejar peticiones OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
