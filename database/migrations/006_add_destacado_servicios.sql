-- Migration 006: Agregar campo "destacado" a servicios
-- Permite marcar manualmente qué servicios se muestran en la landing (máx 6)

ALTER TABLE servicios
ADD COLUMN destacado TINYINT(1) NOT NULL DEFAULT 0 AFTER imagen;

-- Índice para consulta rápida de destacados en la landing
CREATE INDEX idx_destacado ON servicios (destacado);
