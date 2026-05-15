-- =============================================
-- Migration 014: Limpieza post-mapping a maquinas
--
-- Se aplica DESPUES de que el mapping inicial de servicios -> id_maquina
-- esta completo (0 huerfanos). Baja flags legacy de jornada en las
-- categorias que pasaron a depender de maquinas, desactiva la categoria
-- virtual 13, y marca campos legacy como DEPRECATED.
--
-- No es destructiva: no borra columnas ni datos. Migration 015 hara el
-- drop final tras una semana de produccion estable.
--
-- Fecha: 2026-05-15
-- =============================================

-- ── 1. Bajar requiere_jornada en categorias que ahora dependen de maquinas ──
-- Cat 1 (Depilacion): todos sus servicios apuntan a maquina Laser.
UPDATE categorias_servicios SET requiere_jornada = 0 WHERE id = 1;

-- ── 2. Desactivar cat 13 "Tratamiento con equipo" ──
-- Era un contenedor virtual de jornadas para Criolipo/VelaSlim/packs.
-- Tras el mapping, esos servicios apuntan a sus maquinas reales.
UPDATE categorias_servicios SET activo = 0, requiere_jornada = 0 WHERE id = 13;

-- ── 3. Marcar campos legacy como DEPRECATED (cosmetico, no destructivo) ──
ALTER TABLE servicios MODIFY id_grupo_jornada INT NULL
  COMMENT 'DEPRECATED: usar id_maquina';

ALTER TABLE servicios MODIFY disponibilidad ENUM('auto','normal','jornada') NOT NULL DEFAULT 'auto'
  COMMENT 'DEPRECATED: usar id_maquina IS NULL';

-- =============================================
-- DOWN (rollback manual):
--
-- UPDATE categorias_servicios SET requiere_jornada = 1 WHERE id = 1;
-- UPDATE categorias_servicios SET activo = 1, requiere_jornada = 1 WHERE id = 13;
-- ALTER TABLE servicios MODIFY id_grupo_jornada INT NULL
--   COMMENT 'Si no es NULL, el servicio requiere jornada del grupo indicado';
-- ALTER TABLE servicios MODIFY disponibilidad ENUM('auto','normal','jornada') NOT NULL DEFAULT 'auto'
--   COMMENT 'auto=hereda de categoría, normal=calendario libre, jornada=requiere jornada activa';
-- =============================================
