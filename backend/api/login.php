<?php
// backend/api/login.php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';

// Obtener datos del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['correo']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Correo y contraseña requeridos.']);
    exit;
}

$correo = $conn->real_escape_string($data['correo']);
$password = $data['password'];

// Buscar usuario por correo
$sql = "SELECT user_id, nombre, apellido, correo, telefono, direccion, password, rol FROM usuarios WHERE correo = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    // Verificar contraseña
    if (password_verify($password, $user['password'])) {
        // Iniciar sesión (config.php ya hace session_start si es necesario, pero aseguramos variables)
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];

        // No enviar el hash de la contraseña en la respuesta
        unset($user['password']);

        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta.']);
    }
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado o credenciales inválidas.']);
}

$stmt->close();
$conn->close();
?>