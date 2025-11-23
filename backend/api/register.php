<?php
header('Content-Type: application/json');

// Permitir peticiones desde cualquier origen (CORS simple para pruebas)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}


require_once __DIR__ . '/../config/config.php';

$data = json_decode(file_get_contents('php://input'), true);

$nombre = $data['nombre'] ?? '';
$apellido = $data['apellido'] ?? '';
$correo = $data['correo'] ?? '';
$telefono = $data['telefono'] ?? '';
$direccion = $data['direccion'] ?? '';
$password = $data['password'] ?? '';
$rol = $data['rol'] ?? '';

// Validación simple
if (!$nombre || !$apellido || !$correo || !$password || !$rol) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']);
    exit;
}

// Validar que el correo no exista previamente
$correo = $conn->real_escape_string($correo);
$sql_check = "SELECT user_id FROM usuarios WHERE correo = '$correo' LIMIT 1";
$result_check = $conn->query($sql_check);
if ($result_check && $result_check->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'error' => 'El correo ya está registrado']);
    exit;
}

// Hash de la contraseña
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insertar usuario
$sql = "INSERT INTO usuarios (nombre, apellido, correo, telefono, direccion, password, rol) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error en el servidor']);
    exit;
}
$stmt->bind_param('sssssss', $nombre, $apellido, $correo, $telefono, $direccion, $password_hash, $rol);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;
    // Si el rol es paciente, insertar en la tabla paciente
    if ($rol === 'paciente') {
        $sql_paciente = "INSERT INTO paciente (id_paciente) VALUES (?)";
        $stmt_paciente = $conn->prepare($sql_paciente);
        if ($stmt_paciente) {
            $stmt_paciente->bind_param('i', $user_id);
            $stmt_paciente->execute();
            $stmt_paciente->close();
        }
    }
    // Si el rol es psicologo, insertar en la tabla psicologo (con valores por defecto)
    if ($rol === 'psicologo') {
        $sql_psicologo = "INSERT INTO psicologo (id_psicologo, cedula, consultorio_id, costo, modalidad) VALUES (?, '', NULL, 0, 'presencial')";
        $stmt_psicologo = $conn->prepare($sql_psicologo);
        if ($stmt_psicologo) {
            $stmt_psicologo->bind_param('i', $user_id);
            $stmt_psicologo->execute();
            $stmt_psicologo->close();
        }
    }
    echo json_encode([
        'success' => true,
        'mensaje' => 'Usuario registrado correctamente',
        'usuario' => [
            'user_id' => $user_id,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'correo' => $correo,
            'telefono' => $telefono,
            'direccion' => $direccion,
            'rol' => $rol
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'No se pudo registrar el usuario']);
}
$stmt->close();
$conn->close();
