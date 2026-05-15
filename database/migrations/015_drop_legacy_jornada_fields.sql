-- =============================================
-- Migration 015: Drop final de campos legacy de jornada
--
-- Se aplica DESPUES del refactor del codigo que removio toda referencia
-- a servicios.id_grupo_jornada y servicios.disponibilidad.
--
-- Tras 014 esos campos quedaron marcados DEPRECATED. Esta migration
-- los elimina definitivamente y borra la categoria 13 (que ya estaba
-- desactivada por 014).
--
-- Fecha: 2026-05-15
-- =============================================

-- ── 1. Drop FK y columna servicios.id_grupo_jornada ──
ALTER TABLE servicios DROP FOREIGN KEY fk_servicios_grupo_jornada;
ALTER TABLE servicios DROP COLUMN id_grupo_jornada;

-- ── 2. Drop columna servicios.disponibilidad ──
ALTER TABLE servicios DROP COLUMN disponibilidad;

-- ── 3. Eliminar categoria 13 "Tratamiento con equipo" ──
-- Ya estaba desactivada por 014. Sus servicios fueron remapeados a maquinas.
DELETE FROM categorias_servicios WHERE id = 13;

-- =============================================
-- DOWN (rollback manual — requiere recrear datos):
--
-- ALTER TABLE servicios
--   ADD COLUMN id_grupo_jornada INT NULL COMMENT 'DEPRECATED: usar id_maquina',
--   ADD CONSTRAINT fk_servicios_grupo_jornada FOREIGN KEY (id_grupo_jornada) REFERENCES categorias_servicios(id) ON DELETE SET NULL;
-- ALTER TABLE servicios
--   ADD COLUMN disponibilidad ENUM('auto','normal','jornada') NOT NULL DEFAULT 'auto' COMMENT 'DEPRECATED: usar id_maquina IS NULL';
-- INSERT INTO categorias_servicios (id, nombre, descripcion, icono, orden, activo, requiere_jornada)
--   VALUES (13, 'Tratamiento con equipo', '', 'bi-lightning-charge', 11, 0, 0);
-- =============================================
