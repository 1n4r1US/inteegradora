<?php
// backend/api/cita.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Listar todas las citas con datos de paciente y psicólogo (mejorado para evitar nulls)
        $sql = "SELECT c.id_cita, c.fecha, c.hora, c.estado,
                       IFNULL(p.id_paciente, pc.id_paciente) AS id_paciente,
                       IFNULL(u1.nombre, '') AS nombre_paciente, IFNULL(u1.apellido, '') AS apellido_paciente,
                       IFNULL(ps.id_psicologo, psc.id_psicologo) AS id_psicologo,
                       IFNULL(u2.nombre, '') AS nombre_psicologo, IFNULL(u2.apellido, '') AS apellido_psicologo
                FROM cita c
                LEFT JOIN paciente_cita pc ON c.id_cita = pc.id_cita
                LEFT JOIN paciente p ON pc.id_paciente = p.id_paciente
                LEFT JOIN usuarios u1 ON p.id_paciente = u1.user_id
                LEFT JOIN psicologo_cita psc ON c.id_cita = psc.id_cita
                LEFT JOIN psicologo ps ON psc.id_psicologo = ps.id_psicologo
                LEFT JOIN usuarios u2 ON ps.id_psicologo = u2.user_id";
        $result = $conn->query($sql);
        $citas = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Si no hay paciente o psicólogo, no incluir la cita
                if ($row['id_paciente'] && $row['id_psicologo']) {
                    $citas[] = $row;
                }
            }
        }
        echo json_encode(['success' => true, 'citas' => $citas]);
        break;
    case 'POST':
        // Crear cita y asociar paciente y psicólogo
        $data = json_decode(file_get_contents('php://input'), true);
        $fecha = $data['fecha'] ?? null;
        $hora = $data['hora'] ?? null;
        $estado = $data['estado'] ?? 'pendiente';
        $id_paciente = $data['id_paciente'] ?? null;
        $id_psicologo = $data['id_psicologo'] ?? null;
        if (!$fecha || !$hora || !$id_paciente || !$id_psicologo) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']);
            break;
        }
        // Insertar cita
        $sql = "INSERT INTO cita (fecha, hora, estado) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $fecha, $hora, $estado);
        if ($stmt->execute()) {
            $id_cita = $stmt->insert_id;
            // Asociar paciente
            $sql_pc = "INSERT INTO paciente_cita (id_paciente, id_cita) VALUES (?, ?)";
            $stmt_pc = $conn->prepare($sql_pc);
            $stmt_pc->bind_param('ii', $id_paciente, $id_cita);
            $stmt_pc->execute();
            $stmt_pc->close();
            // Asociar psicólogo
            $sql_psc = "INSERT INTO psicologo_cita (id_psicologo, id_cita) VALUES (?, ?)";
            $stmt_psc = $conn->prepare($sql_psc);
            $stmt_psc->bind_param('ii', $id_psicologo, $id_cita);
            $stmt_psc->execute();
            $stmt_psc->close();
            echo json_encode(['success' => true, 'mensaje' => 'Cita creada correctamente', 'id_cita' => $id_cita]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'No se pudo crear la cita']);
        }
        $stmt->close();
        break;
    case 'PUT':
        // Actualizar cita
        $data = json_decode(file_get_contents('php://input'), true);
        $id_cita = $data['id_cita'] ?? null;
        $fecha = $data['fecha'] ?? null;
        $hora = $data['hora'] ?? null;
        $estado = $data['estado'] ?? null;
        if (!$id_cita) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'id_cita es requerido']);
            break;
        }
        $sql = "UPDATE cita SET fecha=?, hora=?, estado=? WHERE id_cita=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssi', $fecha, $hora, $estado, $id_cita);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'mensaje' => 'Cita actualizada correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'No se pudo actualizar la cita']);
        }
        $stmt->close();
        break;
    case 'DELETE':
        // Eliminar cita
        $data = json_decode(file_get_contents('php://input'), true);
        $id_cita = $data['id_cita'] ?? null;
        if (!$id_cita) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'id_cita es requerido']);
            break;
        }
        // Eliminar relaciones
        $sql1 = "DELETE FROM paciente_cita WHERE id_cita=?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param('i', $id_cita);
        $stmt1->execute();
        $stmt1->close();
        $sql2 = "DELETE FROM psicologo_cita WHERE id_cita=?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param('i', $id_cita);
        $stmt2->execute();
        $stmt2->close();
        // Eliminar cita
        $sql = "DELETE FROM cita WHERE id_cita=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id_cita);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'mensaje' => 'Cita eliminada correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'No se pudo eliminar la cita']);
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
