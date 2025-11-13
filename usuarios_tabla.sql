-- Crear tabla de usuarios con roles
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    clave VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'usuario') DEFAULT 'usuario',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear un usuario administrador por defecto (contraseña: admin123)
INSERT INTO usuarios (nombre_usuario, email, clave, rol, estado) 
VALUES ('admin', 'admin@biblioteca.com', '$2y$10$YIjlrBxwxkbf.5d.E3cjcOYLDdlQ0eAxyBvKd0K9Y8JhF5eFMUqFa', 'administrador', 'activo');

-- Nota: La contraseña está hasheada con bcrypt. La contraseña real es: admin123
