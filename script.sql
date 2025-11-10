-- Crear la Base de Datos
CREATE DATABASE biblioteca_db;
USE biblioteca_db;

-- Crear la Tabla 1 (Autores)
CREATE TABLE autores (
    id_autor INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    nacionalidad VARCHAR(50)
);

-- Crear la Tabla 2 (Libros)
CREATE TABLE libros (
    id_libro INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    anio_publicacion INT,
    id_autor INT,
    FOREIGN KEY (id_autor) REFERENCES autores(id_autor) ON DELETE CASCADE
);

-- Insertar algunos datos de prueba para la Nota 4
INSERT INTO autores (nombre, nacionalidad) VALUES ('Gabriel García Márquez', 'Colombiana');
INSERT INTO libros (titulo, anio_publicacion, id_autor) VALUES ('Cien años de soledad', 1967, 1);