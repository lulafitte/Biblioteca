-- Añadir columna created_by a la tabla libros y FK a usuarios
ALTER TABLE libros
  ADD COLUMN created_by INT NULL AFTER id_autor,
  ADD CONSTRAINT fk_libros_created_by FOREIGN KEY (created_by) REFERENCES usuarios(id_usuario) ON DELETE SET NULL;

-- Añadir índice para búsquedas por creador
CREATE INDEX idx_libros_created_by ON libros(created_by);
