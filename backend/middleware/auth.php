<?php
// backend/middleware/auth.php
// Middleware de autenticación y autorización

/**
 * Verifica que el usuario esté autenticado (tiene sesión activa)
 * Si no está autenticado, retorna 401 y termina la ejecución
 */
function requireAuth()
{
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'No autenticado. Por favor inicia sesión.'
        ]);
        exit;
    }
}

/**
 * Verifica que el usuario tenga un rol específico
 * @param string $role - El rol requerido ('paciente', 'psicologo', 'admin')
 */
function requireRole($role)
{
    requireAuth(); // Primero verificar que esté autenticado

    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== $role) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'Acceso denegado. No tienes permisos para esta acción.'
        ]);
        exit;
    }
}

/**
 * Obtiene los datos del usuario actual de la sesión
 * @return array|null - Datos del usuario o null si no está autenticado
 */
function getCurrentUser()
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    return [
        'user_id' => $_SESSION['user_id'],
        'nombre' => $_SESSION['nombre'] ?? '',
        'rol' => $_SESSION['rol'] ?? ''
    ];
}

/**
 * Verifica que el usuario sea el propietario del recurso
 * @param int $resourceUserId - ID del usuario dueño del recurso
 */
function requireOwnership($resourceUserId)
{
    requireAuth();

    if ($_SESSION['user_id'] != $resourceUserId) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'No tienes permiso para modificar este recurso.'
        ]);
        exit;
    }
}
?>