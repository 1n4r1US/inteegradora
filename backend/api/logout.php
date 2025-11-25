<?php
// backend/api/logout.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

session_destroy();
echo json_encode(['success' => true, 'message' => 'Sesión cerrada']);
