-- Intentar renombrar la columna contrasena a clave (si existe)
ALTER TABLE usuarios CHANGE contrasena clave VARCHAR(255) NOT NULL;

-- Asegurar el tipo de la columna
ALTER TABLE usuarios MODIFY clave VARCHAR(255) NOT NULL;

-- Crear la tabla si no existe (definición segura con 'clave')
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    clave VARCHAR(255) NOT NULL,
    rol ENUM('administrador','usuario') DEFAULT 'usuario' NOT NULL,
    estado ENUM('activo','inactivo') DEFAULT 'activo' NOT NULL,
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertar admin si no existe (evita duplicados por UNIQUE constraint)
INSERT INTO usuarios (nombre_usuario, email, clave, rol, estado)
SELECT 'admin', 'admin@biblioteca.com', '$2y$10$YIjlrBxwxkbf.5d.E3cjcOYLDdlQ0eAxyBvKd0K9Y8JhF5eFMUqFa', 'administrador', 'activo'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE nombre_usuario = 'admin');
