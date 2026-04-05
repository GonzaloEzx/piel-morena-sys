-- Migration 011: Agregar campo disponibilidad a servicios
-- Permite override per-service del comportamiento de jornada:
--   'auto'    = hereda de categoría (comportamiento actual, backward-compatible)
--   'normal'  = calendario libre siempre
--   'jornada' = requiere jornada activa
--
-- Fecha: 2026-04-04

ALTER TABLE servicios
ADD COLUMN disponibilidad ENUM('auto','normal','jornada') NOT NULL DEFAULT 'auto'
COMMENT 'auto=hereda de categoría, normal=calendario libre, jornada=requiere jornada activa'
AFTER id_grupo_jornada;
