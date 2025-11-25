# Bienestar Emocional - Sistema de Gestión de Práctica Psicológica

Sistema web para la gestión de consultas psicológicas, permitiendo a pacientes agendar citas y a psicólogos administrar su práctica profesional.

## 🚀 Características Principales

- ✅ **Autenticación segura** con sesiones PHP
- ✅ **Gestión de citas** para pacientes y psicólogos
- ✅ **Perfiles de usuario** con información detallada
- ✅ **Sistema de notificaciones** toast moderno
- ✅ **Validaciones en tiempo real**
- ✅ **Control de acceso basado en roles**
- ✅ **Transacciones de base de datos** para integridad de datos

## 📋 Requisitos

- **WAMP Server** (Windows) o LAMP/MAMP (Linux/Mac)
  - PHP 7.4 o superior
  - MySQL 5.7 o superior
  - Apache 2.4 o superior
- Navegador web moderno (Chrome, Firefox, Edge)

## 🛠️ Instalación

### 1. Clonar/Descargar el Proyecto

Coloca el proyecto en la carpeta `www` de WAMP:
```
c:\wamp64\www\integradora-backend\
```

### 2. Configurar la Base de Datos

1. Abre **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Crea una nueva base de datos llamada `psicologia`
3. Importa el archivo SQL:
   - Clic en la pestaña **"SQL"**
   - Copia y pega el contenido de `backend/psicologia.sql`
   - Haz clic en **"Continuar"**

### 3. Verificar Configuración

El archivo `backend/config/config.php` ya está configurado para WAMP por defecto:
```php
$host = 'localhost';
$user = 'root';
$password = ''; // Vacío por defecto en WAMP
$database = 'psicologia';
```

### 4. Iniciar WAMP

- Asegúrate de que el ícono de WAMP esté **verde**
- Si no, haz clic derecho → "Start All Services"

### 5. Acceder al Sistema

Abre tu navegador y ve a:
```
http://localhost/integradora-backend/
```

## 👥 Roles de Usuario

### Paciente
- Ver y agendar citas
- Actualizar perfil personal
- Ver historial de citas
- Ver información de psicólogos

### Psicólogo
- Gestionar agenda de citas
- Actualizar perfil profesional
- Administrar consultorios
- Ver pacientes asignados

## 📁 Estructura del Proyecto

```
integradora-backend/
├── backend/
│   ├── api/                    # Endpoints de la API REST
│   │   ├── cita.php           # CRUD de citas
│   │   ├── paciente.php       # CRUD de pacientes
│   │   ├── psicologo.php      # CRUD de psicólogos
│   │   ├── consultorio.php    # CRUD de consultorios
│   │   ├── login.php          # Autenticación
│   │   ├── register.php       # Registro de usuarios
│   │   ├── check_auth.php     # Verificar sesión
│   │   └── logout.php         # Cerrar sesión
│   ├── config/
│   │   └── config.php         # Configuración de BD
│   ├── middleware/
│   │   └── auth.php           # Middleware de autenticación
│   └── psicologia.sql         # Script de base de datos
├── js/
│   ├── api.js                 # Cliente API (fetch)
│   ├── config.js              # Configuración frontend
│   ├── notifications.js       # Sistema de notificaciones toast
│   ├── validators.js          # Validaciones de formularios
│   └── modal-manager.js       # Gestor de modales
├── assets/                    # Imágenes y recursos
├── index.html                 # Página principal
├── auth.html                  # Login/Registro
├── dashboard.html             # Panel de usuario
├── script.js                  # JavaScript principal
├── styles.css                 # Estilos CSS
└── README.md                  # Este archivo
```

## 🔒 Seguridad

### Autenticación
- **Sesiones PHP** con cookies HttpOnly
- Middleware de autenticación en todas las APIs
- Validación de sesión en cada request

### Control de Acceso
- **Basado en roles**: paciente, psicólogo, admin
- **Ownership validation**: usuarios solo pueden editar su propia información
- Restricciones específicas por endpoint

### Base de Datos
- **Prepared statements** para prevenir SQL injection
- **Transacciones** en operaciones críticas
- Contraseñas hasheadas con `password_hash()`

## 🎨 Características de UX

### Sistema de Notificaciones Toast
Reemplaza los `alert()` tradicionales con notificaciones modernas:
- Auto-dismiss después de 3 segundos
- 4 tipos: success, error, info, warning
- Barra de progreso animada
- Responsive

### Validaciones en Tiempo Real
- Validación de email
- Validación de teléfono
- Prevención de fechas pasadas
- Feedback visual inmediato

### Estados de Carga
- Spinners en botones durante operaciones async
- Overlay de carga global
- Botones deshabilitados durante procesamiento

## 📡 API Endpoints

### Autenticación
```
POST   /backend/api/login.php        # Iniciar sesión
POST   /backend/api/register.php     # Registrar usuario
GET    /backend/api/check_auth.php   # Verificar sesión
POST   /backend/api/logout.php       # Cerrar sesión
```

### Citas
```
GET    /backend/api/cita.php          # Listar citas
POST   /backend/api/cita.php          # Crear cita
PUT    /backend/api/cita.php          # Actualizar cita
DELETE /backend/api/cita.php          # Eliminar cita
```

### Pacientes
```
GET    /backend/api/paciente.php      # Listar pacientes
PUT    /backend/api/paciente.php      # Actualizar paciente
```

### Psicólogos
```
GET    /backend/api/psicologo.php     # Listar psicólogos
PUT    /backend/api/psicologo.php     # Actualizar psicólogo
```

### Consultorios
```
GET    /backend/api/consultorio.php   # Listar consultorios
POST   /backend/api/consultorio.php   # Crear consultorio
PUT    /backend/api/consultorio.php   # Actualizar consultorio
DELETE /backend/api/consultorio.php   # Eliminar consultorio
```

> **Nota**: Todos los endpoints (excepto login y register) requieren autenticación.

## 🧪 Pruebas

### Flujo de Prueba Completo

1. **Registro**
   - Ve a `http://localhost/integradora-backend/auth.html`
   - Completa el formulario de registro
   - Verifica que aparezca un toast verde de éxito
   - Deberías ser redirigido al dashboard

2. **Login**
   - Cierra sesión
   - Vuelve a `auth.html`
   - Ingresa tus credenciales
   - Verifica redirección al dashboard

3. **Agendar Cita** (como paciente)
   - En el dashboard, haz clic en "Agendar nueva cita"
   - Selecciona un psicólogo
   - Elige fecha y hora
   - Verifica que aparezca en "Próxima cita"

4. **Gestionar Citas** (como psicólogo)
   - Registra un usuario con rol "Psicólogo"
   - Configura tu perfil profesional
   - Ve tus citas programadas
   - Cambia el estado de una cita

## 🐛 Solución de Problemas

### Error: "No se puede conectar a la base de datos"
- Verifica que WAMP esté corriendo (ícono verde)
- Revisa las credenciales en `backend/config/config.php`
- Asegúrate de que la base de datos `psicologia` exista

### Error: "404 Not Found"
- Verifica que la carpeta esté en `c:\wamp64\www\integradora-backend`
- Usa la URL correcta: `http://localhost/integradora-backend/...`

### Error: "No autenticado"
- Asegúrate de haber iniciado sesión
- Verifica que las cookies estén habilitadas
- Revisa que `PHPSESSID` exista en las cookies (F12 → Application → Cookies)

### Los módulos ES6 no cargan
- Asegúrate de acceder vía `http://localhost/...` (no `file://`)
- Verifica que los tags `<script>` tengan `type="module"`

## 📚 Documentación Adicional

- [Setup Instructions](setup_instructions.md) - Guía detallada de instalación
- [Walkthrough](walkthrough.md) - Documentación de mejoras implementadas
- [Analysis Report](analysis_report.md) - Análisis inicial del proyecto

## 🔄 Changelog

### Versión 2.1 (2025-11-25)
- ✅ Implementado middleware de autenticación
- ✅ Agregado control de acceso basado en roles
- ✅ Transacciones en operaciones de BD
- ✅ Sistema de notificaciones toast
- ✅ Validaciones de formularios mejoradas
- ✅ Módulos JavaScript (ES6)
- ✅ Loading states en botones
- ✅ Ownership validation en APIs

### Versión 1.0 (Inicial)
- Sistema básico de autenticación
- CRUD de citas, pacientes y psicólogos
- Dashboard por roles

## 👨‍💻 Desarrollo

### Tecnologías Utilizadas
- **Backend**: PHP 7.4+, MySQL
- **Frontend**: HTML5, CSS3, JavaScript (ES6 Modules)
- **Arquitectura**: REST API, MVC parcial

### Mejores Prácticas Implementadas
- Prepared statements para seguridad
- Separación de responsabilidades (módulos)
- Control de acceso granular
- Validaciones client-side y server-side
- Feedback visual para el usuario

## 📄 Licencia

Este proyecto es de uso educativo.

## 🤝 Contribuciones

Para contribuir al proyecto:
1. Reporta bugs o solicita features
2. Sigue las convenciones de código existentes
3. Documenta tus cambios

---

**Desarrollado con ❤️ para la gestión de prácticas psicológicas**