<?php
// backend/api/check_auth.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => true,
        'user' => [
            'user_id' => $_SESSION['user_id'],
            'nombre' => $_SESSION['nombre'],
            'rol' => $_SESSION['rol']
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
}
