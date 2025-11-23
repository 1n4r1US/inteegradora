<?php
// backend/api/paciente.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Listar todos los pacientes
        $sql = "SELECT u.user_id, u.nombre, u.apellido, u.correo, u.telefono, u.direccion FROM usuarios u INNER JOIN paciente p ON u.user_id = p.id_paciente";
        $result = $conn->query($sql);
        $pacientes = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $pacientes[] = $row;
            }
        }
        echo json_encode(['success' => true, 'pacientes' => $pacientes]);
        break;
    case 'POST':
        // Crear paciente (requiere user_id existente en usuarios)
        $data = json_decode(file_get_contents('php://input'), true);
        $user_id = $data['user_id'] ?? null;
        if (!$user_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'user_id es requerido']);
            break;
        }
        // Verificar que el usuario exista y sea rol paciente
        $sql = "SELECT user_id, rol FROM usuarios WHERE user_id = ? AND rol = 'paciente'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows === 1) {
            // Insertar en paciente
            $sql_insert = "INSERT INTO paciente (id_paciente) VALUES (?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param('i', $user_id);
            if ($stmt_insert->execute()) {
                echo json_encode(['success' => true, 'mensaje' => 'Paciente creado correctamente']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'No se pudo crear el paciente']);
            }
            $stmt_insert->close();
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Usuario no encontrado o no es paciente']);
        }
        $stmt->close();
        break;
    case 'PUT':
        // Actualizar datos del usuario paciente
        $data = json_decode(file_get_contents('php://input'), true);
        $user_id = $data['user_id'] ?? null;
        $nombre = $data['nombre'] ?? null;
        $apellido = $data['apellido'] ?? null;
        $telefono = $data['telefono'] ?? null;
        $direccion = $data['direccion'] ?? null;
        if (!$user_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'user_id es requerido']);
            break;
        }
        $sql = "UPDATE usuarios SET nombre=?, apellido=?, telefono=?, direccion=? WHERE user_id=? AND rol='paciente'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $nombre, $apellido, $telefono, $direccion, $user_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'mensaje' => 'Paciente actualizado correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'No se pudo actualizar el paciente']);
        }
        $stmt->close();
        break;
    case 'DELETE':
        // Eliminar paciente (y usuario si se desea)
        $data = json_decode(file_get_contents('php://input'), true);
        $user_id = $data['user_id'] ?? null;
        if (!$user_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'user_id es requerido']);
            break;
        }
        // Eliminar de paciente
        $sql = "DELETE FROM paciente WHERE id_paciente=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'mensaje' => 'Paciente eliminado correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el paciente']);
        }
        $stmt->close();
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Método no permitido']);
        break;
}
$conn->close();
?>
