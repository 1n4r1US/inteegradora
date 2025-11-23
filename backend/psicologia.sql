CREATE DATABASE IF NOT EXISTS psicologia;
USE psicologia;

-- Tabla de usuarios
CREATE TABLE usuarios (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    direccion VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    rol ENUM('paciente', 'psicologo') NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de pacientes
CREATE TABLE paciente (
    id_paciente INT PRIMARY KEY,
    FOREIGN KEY (id_paciente) REFERENCES usuarios(user_id)
);

-- Tabla de psicólogos
CREATE TABLE psicologo (
    id_psicologo INT PRIMARY KEY,
    cedula VARCHAR(50) NOT NULL,
    consultorio_id INT,
    costo DECIMAL(10,2),
    FOREIGN KEY (id_psicologo) REFERENCES usuarios(user_id)
);

-- Tabla de consultorios
CREATE TABLE consultorio (
    consultorio_id INT AUTO_INCREMENT PRIMARY KEY,
    consultorio VARCHAR(100) NOT NULL,
    direccion VARCHAR(255) NOT NULL
);

-- Relación psicólogo-consultorio (N:M)
CREATE TABLE psicologo_consultorio (
    id_psicologo INT,
    consultorio_id INT,
    PRIMARY KEY (id_psicologo, consultorio_id),
    FOREIGN KEY (id_psicologo) REFERENCES psicologo(id_psicologo),
    FOREIGN KEY (consultorio_id) REFERENCES consultorio(consultorio_id)
);

-- Tabla de citas
CREATE TABLE cita (
    id_cita INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'pendiente'
);

-- Relación paciente-cita (N:M)
CREATE TABLE paciente_cita (
    id_paciente INT,
    id_cita INT,
    PRIMARY KEY (id_paciente, id_cita),
    FOREIGN KEY (id_paciente) REFERENCES paciente(id_paciente),
    FOREIGN KEY (id_cita) REFERENCES cita(id_cita)
);

-- Relación psicólogo-cita (N:M)
CREATE TABLE psicologo_cita (
    id_psicologo INT,
    id_cita INT,
    PRIMARY KEY (id_psicologo, id_cita),
    FOREIGN KEY (id_psicologo) REFERENCES psicologo(id_psicologo),
    FOREIGN KEY (id_cita) REFERENCES cita(id_cita)
);