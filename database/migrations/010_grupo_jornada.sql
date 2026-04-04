-- =============================================
-- Migration 010: Grupo de jornada a nivel servicio
--
-- Permite que servicios individuales requieran jornada
-- sin que toda la categoria lo haga. Resuelve el caso
-- de Criolipólisis/VelaSlim/Packs que deben mostrarse
-- en sus categorias naturales pero usar jornadas.
-- =============================================

-- -----------------------------------------
-- Paso 1: Agregar campo id_grupo_jornada a servicios
-- Si no es NULL, el servicio requiere jornada del grupo indicado
-- -----------------------------------------

ALTER TABLE servicios
ADD COLUMN id_grupo_jornada INT DEFAULT NULL
COMMENT 'FK a categorias_servicios: si no es NULL, el servicio requiere jornada de ese grupo'
AFTER id_categoria;

ALTER TABLE servicios
ADD CONSTRAINT fk_servicios_grupo_jornada
FOREIGN KEY (id_grupo_jornada) REFERENCES categorias_servicios(id) ON DELETE SET NULL;

-- -----------------------------------------
-- Paso 2: Corregir error de migration 009
-- Cat 8 "Corporal" recibio requiere_jornada=1 por error
-- (la migration asumia que cat 8 era Peluqueria)
-- -----------------------------------------

UPDATE categorias_servicios SET requiere_jornada = 0 WHERE id = 8;

-- -----------------------------------------
-- Paso 3: Marcar Peluqueria correctamente
-- Cat 10 en produccion
-- -----------------------------------------

UPDATE categorias_servicios SET requiere_jornada = 1 WHERE id = 10;

-- -----------------------------------------
-- Paso 4: Mover servicios de vuelta a sus categorias naturales
-- y asignar id_grupo_jornada apuntando a cat 13
-- (Trat. Corporales con Equipo = grupo de jornada de equipamiento)
-- -----------------------------------------

-- Criolipólisis Plana (38) y VelaSlim (36) → Tratamientos Corporales (cat 3)
UPDATE servicios
SET id_categoria = 3, id_grupo_jornada = 13
WHERE id IN (38, 36);

-- PACK REDUCTOR (96) y PACK CELULITIS (97) → Packs (id_categoria NULL)
UPDATE servicios
SET id_categoria = NULL, id_grupo_jornada = 13
WHERE id IN (96, 97);

-- -----------------------------------------
-- Nota: Cat 13 "Trat. Corporales con Equipo" queda sin servicios
-- pero sigue activa como grupo de jornada. El admin la ve
-- al crear jornadas; el wizard nunca la muestra (0 servicios).
-- -----------------------------------------

-- Nota: Pack Depilacion Definitiva (id 100) tiene id_categoria NULL
-- y probablemente deberia tener id_grupo_jornada = 1 (Depilacion)
-- ya que usa la maquina laser. Evaluar con el equipo.
