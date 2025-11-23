<?php
// backend/api/psicologo.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Listar todos los psicólogos, aunque no tengan consultorio asignado
        $sql = "SELECT u.user_id, u.nombre, u.apellido, u.correo, u.telefono, u.direccion, p.cedula, p.costo, p.modalidad, p.consultorio_id, IFNULL(c.consultorio, '') AS consultorio, IFNULL(c.direccion, '') AS direccion_consultorio FROM usuarios u INNER JOIN psicologo p ON u.user_id = p.id_psicologo LEFT JOIN consultorio c ON p.consultorio_id = c.consultorio_id";
        $result = $conn->query($sql);
        $psicologos = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $psicologos[] = $row;
            }
        }
        echo json_encode(['success' => true, 'psicologos' => $psicologos]);
        break;
    case 'POST':
        // Crear psicólogo (requiere user_id existente en usuarios y rol psicologo)
        $data = json_decode(file_get_contents('php://input'), true);
        $user_id = $data['user_id'] ?? null;
        $cedula = $data['cedula'] ?? null;
        $consultorio_id = $data['consultorio_id'] ?? null;
        $costo = $data['costo'] ?? null;
        $modalidad = $data['modalidad'] ?? 'presencial';
        if (!$user_id || !$cedula || !$consultorio_id || !$costo || !$modalidad) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']);
            break;
        }
        // Verificar que el usuario exista y sea rol psicologo
        $sql = "SELECT user_id, rol FROM usuarios WHERE user_id = ? AND rol = 'psicologo'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows === 1) {
            // Insertar en psicologo
            $sql_insert = "INSERT INTO psicologo (id_psicologo, cedula, consultorio_id, costo, modalidad) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param('isids', $user_id, $cedula, $consultorio_id, $costo, $modalidad);
            if ($stmt_insert->execute()) {
                echo json_encode(['success' => true, 'mensaje' => 'Psicólogo creado correctamente']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'No se pudo crear el psicólogo']);
            }
            $stmt_insert->close();
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Usuario no encontrado o no es psicólogo']);
        }
        $stmt->close();
        break;
    case 'PUT':
        // Actualizar datos del psicólogo
        $data = json_decode(file_get_contents('php://input'), true);
        $user_id = $data['user_id'] ?? null;
        $cedula = $data['cedula'] ?? null;
        $consultorio_id = $data['consultorio_id'] ?? null;
        $costo = $data['costo'] ?? null;
        $modalidad = $data['modalidad'] ?? 'presencial';
        if (!$user_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'user_id es requerido']);
            break;
        }
        $sql = "UPDATE psicologo SET cedula=?, consultorio_id=?, costo=?, modalidad=? WHERE id_psicologo=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sidss', $cedula, $consultorio_id, $costo, $modalidad, $user_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'mensaje' => 'Psicólogo actualizado correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'No se pudo actualizar el psicólogo']);
        }
        $stmt->close();
        break;
    case 'DELETE':
        // Eliminar psicólogo
        $data = json_decode(file_get_contents('php://input'), true);
        $user_id = $data['user_id'] ?? null;
        if (!$user_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'user_id es requerido']);
            break;
        }
        $sql = "DELETE FROM psicologo WHERE id_psicologo=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'mensaje' => 'Psicólogo eliminado correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el psicólogo']);
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
