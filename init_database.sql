-- ============================================================
-- SCRIPT UNIFICADO: INICIALIZACIÓN COMPLETA DE LA BASE DE DATOS
-- ============================================================
-- Este script contiene:
-- 1. Crear tabla usuarios con columna clave
-- 2. Insertar usuario admin por defecto
-- 3. Alterar tabla libros para agregar columna created_by con FK
-- ============================================================

-- ============================================================
-- 1. CREAR TABLA USUARIOS
-- ============================================================
DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    clave VARCHAR(255) NOT NULL,
    rol ENUM('administrador','usuario') DEFAULT 'usuario' NOT NULL,
    estado ENUM('activo','inactivo') DEFAULT 'activo' NOT NULL,
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre_usuario (nombre_usuario),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- 2. INSERTAR USUARIO ADMINISTRADOR POR DEFECTO
-- ============================================================
-- Usuario: admin
-- Clave: admin123 (hasheada con bcrypt)
INSERT INTO usuarios (nombre_usuario, email, clave, rol, estado) 
VALUES ('admin', 'admin@biblioteca.com', '$2y$10$YIjlrBxwxkbf.5d.E3cjcOYLDdlQ0eAxyBvKd0K9Y8JhF5eFMUqFa', 'administrador', 'activo');

-- ============================================================
-- 3. ALTERAR TABLA LIBROS: AGREGAR CREATED_BY
-- ============================================================
-- Agregar columna created_by si no existe
ALTER TABLE libros
  ADD COLUMN created_by INT NULL AFTER id_autor;

-- Agregar restricción de clave foránea
ALTER TABLE libros
  ADD CONSTRAINT fk_libros_created_by 
  FOREIGN KEY (created_by) REFERENCES usuarios(id_usuario) ON DELETE SET NULL;

-- Crear índice para búsquedas rápidas
CREATE INDEX idx_libros_created_by ON libros(created_by);

-- ============================================================
-- SCRIPT COMPLETADO
-- ============================================================
-- La base de datos está lista para:
-- - Autenticación con login/registro
-- - Gestión de usuarios con roles (admin/usuario)
-- - Rastreo de quién creó cada libro
-- - Control de permisos (solo creador o admin puede editar/eliminar)
-- ============================================================
