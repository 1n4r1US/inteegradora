# Documentación Técnica - Proyecto Bienestar Emocional

## 1. Visión General
Este proyecto es una aplicación web para la gestión de citas psicológicas. Permite la interacción entre dos tipos de usuarios principales: **Pacientes** y **Psicólogos**. La aplicación gestiona el registro de usuarios, autenticación, gestión de perfiles profesionales y el agendamiento de citas.

---

## 2. Arquitectura Tecnológica

El sistema sigue una arquitectura **Cliente-Servidor** clásica:

*   **Frontend (Cliente):** HTML5, CSS3 y JavaScript (Vanilla). Se comunica con el servidor mediante peticiones HTTP asíncronas (`fetch`).
*   **Backend (Servidor):** PHP (Nativo/Vanilla). Expone una API REST que procesa las solicitudes y devuelve respuestas en formato JSON.
*   **Base de Datos:** MySQL. Almacena toda la información relacional.
*   **Servidor Web:** Apache (a través de WampServer).

---

## 3. Base de Datos (MySQL)

El esquema de base de datos (`psicologia`) está diseñado para manejar la herencia de usuarios y las relaciones de citas.

### Tablas Principales:
1.  **`usuarios`**: Tabla maestra. Contiene `user_id`, `nombre`, `apellido`, `correo`, `password` (hasheada), y `rol` ('paciente', 'psicologo').
2.  **`paciente`**: Extensión de usuario. Se relaciona 1:1 con `usuarios`.
3.  **`psicologo`**: Extensión de usuario. Contiene datos profesionales como `cedula`, `costo`, `modalidad` y `consultorio_id`.
4.  **`consultorio`**: Catálogo de lugares físicos de atención.
5.  **`cita`**: Almacena la `fecha`, `hora` y `estado` de la sesión.

### Relaciones:
*   **Usuarios -> Roles:** Al registrarse, se inserta en `usuarios` y simultáneamente en la tabla específica (`paciente` o `psicologo`).
*   **Citas:** Se manejan mediante tablas intermedias (`paciente_cita`, `psicologo_cita`) para vincular quién asiste y quién atiende la cita.

---

## 4. Backend (API PHP)

El backend se encuentra en la carpeta `backend/api/`. Cada archivo PHP actúa como un "Endpoint" que maneja diferentes métodos HTTP (GET, POST, PUT, DELETE).

### Configuración (`backend/config/config.php`)
Establece la conexión a la base de datos usando `mysqli`. Maneja credenciales y codificación UTF-8.

### Endpoints Principales:

#### A. Autenticación
*   **`register.php` (POST):**
    *   Recibe datos del formulario.
    *   Verifica si el correo ya existe.
    *   Hashea la contraseña usando `password_hash()`.
    *   Inserta en `usuarios` y luego en la tabla de rol correspondiente (`paciente` o `psicologo`) dentro de una transacción lógica.
*   **`login.php` (POST):**
    *   Busca al usuario por correo.
    *   Verifica la contraseña con `password_verify()`.
    *   Retorna el objeto usuario (sin la contraseña) para que el frontend lo guarde.

#### B. Gestión de Citas (`cita.php`)
*   **GET:** Lista las citas. Realiza `JOINs` complejos para traer nombres de pacientes y psicólogos en una sola consulta.
*   **POST:** Crea una nueva cita e inserta las relaciones en `paciente_cita` y `psicologo_cita`.
*   **PUT:** Actualiza fecha, hora o estado.
*   **DELETE:** Elimina la cita y sus relaciones.

#### C. Gestión de Psicólogos (`psicologo.php`)
*   **GET:** Obtiene la lista de psicólogos y sus consultorios.
*   **PUT:** Permite al psicólogo actualizar su costo, modalidad y consultorio asignado.

#### D. Consultorios (`consultorio.php`)
*   CRUD completo para gestionar las ubicaciones físicas.

---

## 5. Frontend (Interfaz de Usuario)

La interfaz es una Single Page Application (SPA) híbrida. Aunque tiene múltiples archivos HTML, la lógica fuerte reside en `dashboard.html` que cambia dinámicamente según el usuario.

### Estructura de Archivos:
*   **`index.html`**: Landing page.
*   **`auth.html`**: Formularios de Login y Registro.
*   **`dashboard.html`**: Panel de control principal.
*   **`styles.css`**: Estilos globales y responsivos.
*   **`script.js`**: Lógica general (menú móvil, validaciones simples).

### Lógica del Dashboard (`dashboard.html` + JS embebido):
1.  **Verificación de Sesión:** Al cargar, verifica si existe un objeto `user` en `localStorage`. Si no existe, redirige al login.
2.  **Renderizado Condicional:**
    *   **Si es Paciente:** Muestra paneles de "Próxima Cita", "Historial" y formulario para "Agendar Cita".
    *   **Si es Psicólogo:** Muestra "Agenda de Hoy", "Pacientes Asignados" y configuración de "Perfil Profesional".
3.  **Interacción con API:**
    *   Usa `fetch()` para pedir datos al backend.
    *   Ejemplo: Al abrir el modal de agendar, hace un `fetch` a `psicologo.php` para llenar el `<select>` con los doctores disponibles.

---

## 6. Flujos de Seguridad Implementados

1.  **Inyección SQL:** Se mitigó el riesgo implementando **Sentencias Preparadas** (`prepare`, `bind_param`) en todas las consultas críticas (Login, Registro).
2.  **Protección de Datos:** El endpoint de usuarios (`usuarios.php`) y el login (`login.php`) eliminan explícitamente el campo `password` antes de enviar la respuesta JSON al cliente.
3.  **Validación de Datos:** Se valida en el backend que los campos obligatorios no vengan vacíos antes de procesar.

---

## 7. Cómo probar el proyecto localmente

1.  Asegurar que **WampServer** esté corriendo.
2.  Importar `backend/psicologia.sql` en PHPMyAdmin.
3.  Acceder a `http://localhost/integradora-backend/`.
4.  Registrar un usuario y comenzar el flujo.
