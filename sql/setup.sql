DROP DATABASE IF EXISTS GPG;

CREATE DATABASE GPG;

USE GPG;

CREATE TABLE usuarios(
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    rol BOOLEAN DEFAULT FALSE
);

CREATE TABLE profesores(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    cedula VARCHAR(255),
    telefono VARCHAR(255),
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES usuarios(id)
);

CREATE TABLE asignaturas(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    description VARCHAR(255),
    profesor_id INT,
    FOREIGN KEY (profesor_id) REFERENCES profesores(id)
);

CREATE TABLE periodos(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    cantidad_notas INT,
    asignatura_id INT,
    FOREIGN KEY (asignatura_id) REFERENCES asignaturas(id)
);

CREATE TABLE notas(
    id INT AUTO_INCREMENT PRIMARY KEY,
    valor VARCHAR(255),
    observaciones VARCHAR(255),
    periodo_id INT,
    FOREIGN KEY (periodo_id) REFERENCES periodos(id)
);

CREATE TABLE comentarios(
    id INT AUTO_INCREMENT PRIMARY KEY,
    comentario VARCHAR(255),
    nota_id INT,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES usuarios(id),
    FOREIGN KEY (nota_id) REFERENCES notas(id)
);

CREATE TABLE estudiantes(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    cedula VARCHAR(255),
    telefono VARCHAR(255),
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES usuarios(id)
);

-- PIVOTE ENTRE ESDUDIANTES Y ASIGNATURAS --
CREATE TABLE asignaturas_estudiantes(
    asignatura_id INT,
    estudiante_id INT,
    FOREIGN KEY (asignatura_id) REFERENCES asignaturas(id),
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id)
);

------ CREACION DE DOS PROFESORES ------
INSERT INTO usuarios(email, password, rol) VALUES('juan@gmail.com', '$2y$10$bLCHV/yErBOgu8W/Hf9tUu88tTxCUxf4hS8s2vBNPQKELEf/2c/WK', 1);
INSERT INTO usuarios(email, password, rol) VALUES('pedro@gmail.com', '$2y$10$bLCHV/yErBOgu8W/Hf9tUu88tTxCUxf4hS8s2vBNPQKELEf/2c/WK', 1);
INSERT INTO profesores(name, cedula, telefono, user_id) VALUES('Juan', '123456789', '123456789', 1);
INSERT INTO profesores(name, cedula, telefono, user_id) VALUES('Pedro', '123456789', '123456789', 2);

ALTER TABLE notas ADD estudiante_id INT AFTER periodo_id;
ALTER TABLE notas ADD FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id);

