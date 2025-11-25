<?php
// backend/api/register.php
header('Content-Type: application/json');

// Eliminar CORS wildcard inseguro. Si el frontend está en el mismo dominio, no es necesario.
// Si estuviera en otro dominio, especificarlo explícitamente.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

require_once __DIR__ . '/../config/config.php';

$data = json_decode(file_get_contents('php://input'), true);

$nombre = trim($data['nombre'] ?? '');
$apellido = trim($data['apellido'] ?? '');
$correo = trim($data['correo'] ?? '');
$telefono = trim($data['telefono'] ?? '');
$direccion = trim($data['direccion'] ?? '');
$password = $data['password'] ?? '';
$rol = $data['rol'] ?? '';

// Validación simple mejorada
if (empty($nombre) || empty($apellido) || empty($correo) || empty($password) || empty($rol)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Todos los campos obligatorios deben completarse']);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Formato de correo inválido']);
    exit;
}

// Validar que el correo no exista previamente
$stmt_check = $conn->prepare("SELECT user_id FROM usuarios WHERE correo = ? LIMIT 1");
$stmt_check->bind_param('s', $correo);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check && $result_check->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'error' => 'El correo ya está registrado']);
    exit;
}
$stmt_check->close();

// Hash de la contraseña
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insertar usuario
$sql = "INSERT INTO usuarios (nombre, apellido, correo, telefono, direccion, password, rol) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
    exit;
}
$stmt->bind_param('sssssss', $nombre, $apellido, $correo, $telefono, $direccion, $password_hash, $rol);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;
    $error_secundario = null;

    // Si el rol es paciente, insertar en la tabla paciente
    if ($rol === 'paciente') {
        $sql_paciente = "INSERT INTO paciente (id_paciente) VALUES (?)";
        $stmt_paciente = $conn->prepare($sql_paciente);
        if ($stmt_paciente) {
            $stmt_paciente->bind_param('i', $user_id);
            if (!$stmt_paciente->execute()) {
                $error_secundario = "Error al registrar perfil de paciente.";
                // Log error real: $stmt_paciente->error
            }
            $stmt_paciente->close();
        }
    }
    // Si el rol es psicologo, insertar en la tabla psicologo
    if ($rol === 'psicologo') {
        $sql_psicologo = "INSERT INTO psicologo (id_psicologo, cedula, consultorio_id, costo, modalidad) VALUES (?, 'Pendiente', NULL, 0, 'presencial')";
        $stmt_psicologo = $conn->prepare($sql_psicologo);
        if ($stmt_psicologo) {
            $stmt_psicologo->bind_param('i', $user_id);
            if (!$stmt_psicologo->execute()) {
                $error_secundario = "Error al registrar perfil de psicólogo.";
            }
            $stmt_psicologo->close();
        }
    }

    // Auto-login: Iniciar sesión inmediatamente
    $_SESSION['user_id'] = $user_id;
    $_SESSION['nombre'] = $nombre;
    $_SESSION['rol'] = $rol;

    $response = [
        'success' => true,
        'mensaje' => 'Usuario registrado correctamente',
        'usuario' => [
            'user_id' => $user_id,
            'nombre' => $nombre,
            'rol' => $rol
        ]
    ];

    if ($error_secundario) {
        $response['mensaje'] .= ' (Advertencia: ' . $error_secundario . ')';
    }

    echo json_encode($response);

} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'No se pudo registrar el usuario']);
}

$stmt->close();
$conn->close();
