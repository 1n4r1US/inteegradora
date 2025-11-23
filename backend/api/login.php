<?php
// backend/api/login.php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';

// Obtener datos del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['correo']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Correo y contraseña requeridos.']);
    exit;
}

$correo = $conn->real_escape_string($data['correo']);
$password = $data['password'];

// Buscar usuario por correo
$sql = "SELECT user_id, nombre, apellido, correo, password, rol FROM usuarios WHERE correo = '$correo' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    // Verificar contraseña
    if (password_verify($password, $user['password'])) {
        // No enviar el hash de la contraseña en la respuesta
        unset($user['password']);
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
}

$conn->close();
?>
