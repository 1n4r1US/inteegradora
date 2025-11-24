# Atención Psicológica — Bienestar Emocional

Proyecto integradora: página para un servicio de atención psicológica.

## Frontend

### Contenido creado
- `index.html` — página de inicio con resumen y enlaces a las demás secciones.
- `servicios.html` — página con detalle de servicios.
- `equipo.html` — página con perfiles del equipo.
- `about.html` — página "Sobre Nosotros" (misión y valores).
- `auth.html` — página de iniciar sesión / registrarse (formularios de ejemplo).

### Cómo probar localmente (XAMPP recomendado)
1. Copia la carpeta del proyecto a `C:/xampp/htdocs/integradora-backend` o la ruta de tu servidor local.
2. Abre XAMPP y enciende **Apache** y **MySQL**.
3. Abre tu navegador y entra a:
	 - `http://localhost/integradora-backend/index.html` (página principal)
	 - `http://localhost/integradora-backend/auth.html` (login/registro)
	 - `http://localhost/integradora-backend/dashboard.html` (dashboard, requiere login)
4. El frontend se comunica con el backend usando AJAX (fetch) a las rutas PHP.

### Subir el código a GitHub
1. Abre PowerShell o CMD en la carpeta del proyecto.
2. Inicializa el repositorio:
	 ```powershell
	 git init
	 git add .
	 git commit -m "Proyecto inicial: plataforma psicológica PHP/Bootstrap"
	 ```
3. Crea un repositorio en GitHub y sigue las instrucciones para conectar tu repo local:
	 ```powershell
	 git remote add origin https://github.com/usuario/integradora-backend.git
	 git branch -M main
	 git push -u origin main
	 ```
4. (Opcional) Agrega un archivo `.gitignore` para excluir archivos temporales, por ejemplo:
	 ```
	 /backend/config/config.php
	 *.log
	 *.env
	 ```

---

## Backend: API Psicología

### Descripción
API RESTful para la gestión de usuarios, pacientes, psicólogos, consultorios y citas en una clínica psicológica.

### Endpoints principales

#### Autenticación
- **POST** `/backend/api/login.php`
	- **Body:** `{ "correo": "usuario@ejemplo.com", "password": "123456" }`
	- **Respuesta exitosa:**
		```json
		{ "success": true, "user": { "user_id": 1, "nombre": "Juan", ... } }
		```

#### Registro de usuario
- **POST** `/backend/api/register.php`
	- **Body:**
		```json
		{
			"nombre": "Ana",
			"apellido": "López",
			"correo": "ana@ejemplo.com",
			"telefono": "5559876543",
			"direccion": "Av. Siempre Viva 742",
			"password": "mipasswordseguro",
			"rol": "paciente"
		}
		```
	- **Respuesta exitosa:**
		```json
		{ "success": true, "mensaje": "Usuario registrado correctamente", "usuario": { ... } }
		```

#### Usuarios
- **GET** `/backend/api/usuarios.php` — Lista todos los usuarios.

#### Pacientes
- **GET** `/backend/api/paciente.php` — Lista pacientes
- **POST** `/backend/api/paciente.php` — Crea paciente (automático al registrar usuario con rol paciente)
- **PUT** `/backend/api/paciente.php` — Actualiza paciente
- **DELETE** `/backend/api/paciente.php` — Elimina paciente

#### Psicólogos
- **GET** `/backend/api/psicologo.php` — Lista psicólogos
- **POST** `/backend/api/psicologo.php` — Crea psicólogo
- **PUT** `/backend/api/psicologo.php` — Actualiza psicólogo
- **DELETE** `/backend/api/psicologo.php` — Elimina psicólogo

#### Consultorios
- **GET** `/backend/api/consultorio.php` — Lista consultorios
- **POST** `/backend/api/consultorio.php` — Crea consultorio
- **PUT** `/backend/api/consultorio.php` — Actualiza consultorio
- **DELETE** `/backend/api/consultorio.php` — Elimina consultorio

#### Citas
- **GET** `/backend/api/cita.php` — Lista citas
- **POST** `/backend/api/cita.php` — Crea cita
- **PUT** `/backend/api/cita.php` — Actualiza cita
- **DELETE** `/backend/api/cita.php` — Elimina cita

### Notas
- Todas las respuestas son en formato JSON.
- Los endpoints requieren los parámetros indicados en el body (formato JSON).
- El registro de pacientes es automático al crear un usuario con rol "paciente".
- Para crear una cita, el paciente y psicólogo deben existir y estar correctamente relacionados.

### Ejemplos detallados de uso

#### Ejemplo: Registrar usuario (paciente)
**POST** `/backend/api/register.php`
```json
{
	"nombre": "Ana",
	"apellido": "López",
	"correo": "ana@ejemplo.com",
	"telefono": "5559876543",
	"direccion": "Av. Siempre Viva 742",
	"password": "mipasswordseguro",
	"rol": "paciente"
}
```
**Respuesta:**
```json
{
	"success": true,
	"mensaje": "Usuario registrado correctamente",
	"usuario": {
		"user_id": 2,
		"nombre": "Ana",
		"apellido": "López",
		"correo": "ana@ejemplo.com",
		"telefono": "5559876543",
		"direccion": "Av. Siempre Viva 742",
		"rol": "paciente"
	}
}
```

#### Ejemplo: Crear cita
**POST** `/backend/api/cita.php`
```json
{
	"fecha": "2025-12-01",
	"hora": "10:00:00",
	"id_paciente": 2,
	"id_psicologo": 3
}
```
**Respuesta:**
```json
{
	"success": true,
	"mensaje": "Cita creada correctamente",
	"id_cita": 1
}
```

#### Ejemplo: Error de validación
```json
{
	"success": false,
	"error": "Todos los campos son obligatorios"
}
```

### Notas de seguridad y buenas prácticas
- Usa HTTPS en producción para proteger los datos transmitidos.
- Las contraseñas se almacenan usando `password_hash` y se validan con `password_verify`.
- Valida y sanitiza todos los datos recibidos en el backend.
- No expongas información sensible en las respuestas (por ejemplo, hashes de contraseñas).
- Implementa control de acceso según el rol del usuario para proteger endpoints sensibles.
- Considera agregar autenticación basada en tokens (JWT) para mayor seguridad.
- Realiza respaldos periódicos de la base de datos.

---
