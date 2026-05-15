-- =============================================
-- Migration 013: Maquinas alquiladas y sus alquileres
--
-- Introduce el concepto de "maquina alquilada" como entidad de primer
-- orden, paralelo a las jornadas existentes (que pasan a aplicar solo
-- a personal externo: Naila, Nathalia).
--
-- Sin INSERTs: el catalogo de maquinas se construye desde el panel.
-- El mapping de servicios.id_maquina lo hace Mari post-deploy.
--
-- Fecha: 2026-05-15
-- =============================================

-- ── 1. Catalogo de maquinas ──
CREATE TABLE maquinas (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    color       VARCHAR(7) NOT NULL DEFAULT '#8A7650',
    icono       VARCHAR(50) NULL,
    activo      TINYINT(1) NOT NULL DEFAULT 1,
    notas       TEXT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 2. Alquileres (disponibilidad de cada maquina por fecha) ──
CREATE TABLE maquinas_alquileres (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    id_maquina   INT NOT NULL,
    fecha        DATE NOT NULL,
    hora_inicio  TIME NOT NULL DEFAULT '08:00:00',
    hora_fin     TIME NOT NULL DEFAULT '20:00:00',
    estado       ENUM('activo','cancelado') NOT NULL DEFAULT 'activo',
    notas        TEXT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_maquina_fecha (id_maquina, fecha),
    KEY idx_fecha_estado (fecha, estado),
    KEY idx_maquina (id_maquina),
    CONSTRAINT fk_alquiler_maquina FOREIGN KEY (id_maquina) REFERENCES maquinas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 3. servicios.id_maquina ──
-- Si NO NULL, el servicio depende de esa maquina: solo se reserva en fechas con alquiler activo.
-- Si NULL, se resuelve por la via clasica (jornada por categoria o calendario libre).
ALTER TABLE servicios
ADD COLUMN id_maquina INT NULL COMMENT 'FK a maquinas: si no es NULL, el servicio requiere alquiler activo'
AFTER id_grupo_jornada;

ALTER TABLE servicios
ADD CONSTRAINT fk_servicios_maquina FOREIGN KEY (id_maquina) REFERENCES maquinas(id) ON DELETE SET NULL;

-- =============================================
-- DOWN (rollback manual):
--
-- ALTER TABLE servicios DROP FOREIGN KEY fk_servicios_maquina;
-- ALTER TABLE servicios DROP COLUMN id_maquina;
-- DROP TABLE maquinas_alquileres;
-- DROP TABLE maquinas;
-- =============================================
