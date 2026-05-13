-- Creación de la base de datos
CREATE DATABASE MiBaseDeDatos;

-- Selección de la base de datos para usarla
USE MiBaseDeDatos;

-- Creación de la tabla Usuarios
CREATE TABLE Usuarios (
    ID_usuario INT PRIMARY KEY,
    Nombre VARCHAR(100),
    Apellido1 VARCHAR(100),
    Apellido2 VARCHAR(100),
    Correo VARCHAR(255),
    Contraseña VARCHAR(255)
);

-- Creación de la tabla Curso
CREATE TABLE Curso (
    ID_curso INT PRIMARY KEY,
    Nombre VARCHAR(255)
);

-- Creación de la tabla Reserva
CREATE TABLE Reserva (
    ID_reservas INT PRIMARY KEY,
    Fecha_reservada DATE,
    Hora_reservada VARCHAR(10),
    id_usuario INT,  /* Foreing key -> Usuario */
    id_curso INT /* Foreing key -> Curso */
);

-- Adición de las claves foráneas a la tabla Reserva
ALTER TABLE Reserva
ADD CONSTRAINT fk_reserva_usuario
FOREIGN KEY (id_usuario) REFERENCES Usuarios(ID_usuario);

ALTER TABLE Reserva
ADD CONSTRAINT fk_reserva_curso
FOREIGN KEY (id_curso) REFERENCES Curso(ID_curso);
