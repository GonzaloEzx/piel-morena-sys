-- Migration 012: Rediseño de promociones como bundles + pivot + categoría Packs
--
-- Cambios:
--   1. DROP y recrear tabla promociones (de descuentos a bundles)
--   2. Crear tabla pivot promocion_servicios
--   3. Insertar categoría "Packs y Promociones"
--   4. Mover packs/combos existentes a la nueva categoría
--   5. Setear disponibilidad explícita en packs con id_grupo_jornada
--
-- Fecha: 2026-04-05

-- ── 1. Rediseño tabla promociones ──
DROP TABLE IF EXISTS promociones;

CREATE TABLE promociones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    precio_pack DECIMAL(10,2) NOT NULL COMMENT 'Precio del bundle',
    duracion_estimada INT NOT NULL DEFAULT 60 COMMENT 'Duración en minutos',
    fecha_inicio DATE DEFAULT NULL COMMENT 'Inicio de vigencia (NULL = sin límite)',
    fecha_fin DATE DEFAULT NULL COMMENT 'Fin de vigencia (NULL = sin límite)',
    imagen VARCHAR(255) DEFAULT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    id_servicio_generado INT DEFAULT NULL COMMENT 'FK al servicio que representa este pack en el wizard',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_activo (activo),
    KEY idx_vigencia (fecha_inicio, fecha_fin),
    CONSTRAINT fk_promo_servicio FOREIGN KEY (id_servicio_generado) REFERENCES servicios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 2. Tabla pivot promocion_servicios ──
CREATE TABLE promocion_servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_promocion INT NOT NULL,
    id_servicio INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1 COMMENT 'Cantidad de sesiones incluidas',
    UNIQUE KEY uk_promo_servicio (id_promocion, id_servicio),
    CONSTRAINT fk_ps_promocion FOREIGN KEY (id_promocion) REFERENCES promociones(id) ON DELETE CASCADE,
    CONSTRAINT fk_ps_servicio FOREIGN KEY (id_servicio) REFERENCES servicios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 3. Categoría "Packs y Promociones" ──
INSERT INTO categorias_servicios (nombre, descripcion, icono, orden, activo, requiere_jornada)
VALUES ('Packs y Promociones', 'Combos y packs de servicios a precio especial', 'bi-gift', 12, 1, 0);

SET @cat_packs = LAST_INSERT_ID();

-- ── 4. Mover packs y combos existentes a la nueva categoría ──
-- PACKs (96, 97, 100) y COMBOs de depilación (64, 65, 66, 67)
UPDATE servicios SET id_categoria = @cat_packs WHERE id IN (64, 65, 66, 67, 96, 97, 100);

-- ── 5. Setear disponibilidad explícita en packs con id_grupo_jornada ──
-- Packs que ya tienen grupo de jornada: marcar como 'jornada'
UPDATE servicios SET disponibilidad = 'jornada' WHERE id IN (96, 97, 100) AND id_grupo_jornada IS NOT NULL;

-- COMBOs de depilación: marcar como 'jornada' con grupo de Depilación (id=1)
UPDATE servicios SET disponibilidad = 'jornada', id_grupo_jornada = 1 WHERE id IN (64, 65, 66, 67);
