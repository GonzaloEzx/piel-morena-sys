-- =============================================
-- Migration 016: Agenda por profesional y reserva como invitada (MVP v2.0)
--
-- 1. usuarios.email pasa a ser opcional: la clienta puede reservar solo con
--    nombre y WhatsApp. UNIQUE admite varios NULL.
-- 2. usuarios.telefono_norm: ultimos 10 digitos del telefono (codigo de area
--    + numero) para reconocer a la clienta aunque lo escriba distinto.
-- 3. citas.token_gestion: link para que la clienta vea, cancele o
--    reprograme su turno. Se recuperan los tokens viejos guardados en notas.
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

ALTER TABLE citas
  ADD COLUMN token_gestion CHAR(32) NULL AFTER notas,
  ADD UNIQUE INDEX uk_token_gestion (token_gestion);

UPDATE citas
   SET token_gestion = SUBSTRING(notas, 7, 32)
 WHERE notas REGEXP '^token:[0-9a-f]{32}';

-- =============================================
-- DOWN (rollback manual):
--
-- ALTER TABLE citas DROP INDEX uk_token_gestion, DROP COLUMN token_gestion;
-- ALTER TABLE usuarios DROP INDEX idx_telefono_norm, DROP COLUMN telefono_norm;
-- UPDATE usuarios SET email = CONCAT('sin-email-', id, '@pielmorena.invalid') WHERE email IS NULL;
-- ALTER TABLE usuarios MODIFY email VARCHAR(150) NOT NULL;
-- =============================================
