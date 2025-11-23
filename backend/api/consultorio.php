<?php
// backend/api/consultorio.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Listar todos los consultorios
        $sql = "SELECT consultorio_id, consultorio, direccion FROM consultorio";
        $result = $conn->query($sql);
        $consultorios = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $consultorios[] = $row;
            }
        }
        echo json_encode(['success' => true, 'consultorios' => $consultorios]);
        break;
    case 'POST':
        // Crear consultorio
        $data = json_decode(file_get_contents('php://input'), true);
        $consultorio = $data['consultorio'] ?? null;
        $direccion = $data['direccion'] ?? null;
        if (!$consultorio || !$direccion) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']);
            break;
        }
        $sql = "INSERT INTO consultorio (consultorio, direccion) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $consultorio, $direccion);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'mensaje' => 'Consultorio creado correctamente', 'consultorio_id' => $stmt->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'No se pudo crear el consultorio']);
        }
        $stmt->close();
        break;
    case 'PUT':
        // Actualizar consultorio
        $data = json_decode(file_get_contents('php://input'), true);
        $consultorio_id = $data['consultorio_id'] ?? null;
        $consultorio = $data['consultorio'] ?? null;
        $direccion = $data['direccion'] ?? null;
        if (!$consultorio_id || !$consultorio || !$direccion) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']);
            break;
        }
        $sql = "UPDATE consultorio SET consultorio=?, direccion=? WHERE consultorio_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $consultorio, $direccion, $consultorio_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'mensaje' => 'Consultorio actualizado correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'No se pudo actualizar el consultorio']);
        }
        $stmt->close();
        break;
    case 'DELETE':
        // Eliminar consultorio
        $data = json_decode(file_get_contents('php://input'), true);
        $consultorio_id = $data['consultorio_id'] ?? null;
        if (!$consultorio_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'consultorio_id es requerido']);
            break;
        }
        $sql = "DELETE FROM consultorio WHERE consultorio_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $consultorio_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'mensaje' => 'Consultorio eliminado correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el consultorio']);
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
