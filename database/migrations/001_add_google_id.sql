-- Migración: Agregar soporte Google OAuth a usuarios
ALTER TABLE usuarios ADD COLUMN google_id VARCHAR(255) DEFAULT NULL AFTER foto;
ALTER TABLE usuarios MODIFY password VARCHAR(255) DEFAULT NULL;
CREATE INDEX idx_google_id ON usuarios(google_id);
