-- =============================================
-- Migration 016: clientas cargadas desde el panel (MVP v2)
--
-- Los turnos se acuerdan por WhatsApp y el salon los carga en el panel.
-- 1. usuarios.email pasa a ser opcional: la clienta se carga con nombre y
--    WhatsApp. UNIQUE admite varios NULL. (El alta del panel ya insertaba
--    NULL en clientas sin email y fallaba contra NOT NULL.)
-- 2. usuarios.telefono_norm: ultimos 10 digitos del telefono (codigo de area
--    + numero) para reconocer a la clienta aunque lo escriba distinto.
--
-- Requiere MySQL 8 o MariaDB 10.0.5+ (REGEXP_REPLACE). No es repetible:
-- antes de correrla, revisar SHOW COLUMNS FROM usuarios.
--
-- Fecha: 2026-10-31
-- =============================================

ALTER TABLE usuarios MODIFY email VARCHAR(150) NULL;

ALTER TABLE usuarios
  ADD COLUMN telefono_norm VARCHAR(10) NULL AFTER telefono,
  ADD INDEX idx_telefono_norm (telefono_norm);

UPDATE usuarios
   SET telefono_norm = RIGHT(REGEXP_REPLACE(telefono, '[^0-9]', ''), 10)
 WHERE telefono IS NOT NULL
   AND LENGTH(REGEXP_REPLACE(telefono, '[^0-9]', '')) >= 10;

-- =============================================
-- DOWN (rollback manual):
--
-- ALTER TABLE usuarios DROP INDEX idx_telefono_norm, DROP COLUMN telefono_norm;
-- UPDATE usuarios SET email = CONCAT('sin-email-', id, '@pielmorena.invalid') WHERE email IS NULL;
-- ALTER TABLE usuarios MODIFY email VARCHAR(150) NOT NULL;
-- =============================================
