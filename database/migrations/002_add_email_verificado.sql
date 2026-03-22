-- Migración: Agregar campo de verificación de email
ALTER TABLE usuarios ADD COLUMN email_verificado TINYINT(1) NOT NULL DEFAULT 0 AFTER google_id;
