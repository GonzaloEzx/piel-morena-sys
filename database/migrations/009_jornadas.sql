-- =============================================
-- Migration 009: Sistema de Jornadas
-- Permite programar dias especificos para servicios
-- que dependen de equipos alquilados o personal eventual.
-- =============================================
-- Categorias afectadas:
--   Depilacion (cat 1)          → maquina alquilada
--   Peluqueria (cat 8)          → Nathalia, dias puntuales
--   Extensiones de Pestanas     → nueva cat, Naila, dias puntuales
--   Trat. Corporales con Equipo → nueva cat, equipo especifico
-- =============================================

-- -----------------------------------------
-- Paso 1: Agregar campo requiere_jornada a categorias
-- -----------------------------------------

ALTER TABLE categorias_servicios
ADD COLUMN requiere_jornada TINYINT(1) NOT NULL DEFAULT 0
COMMENT 'Si es 1, los servicios de esta categoria solo se reservan en fechas con jornada activa';

-- -----------------------------------------
-- Paso 2: Crear nuevas categorias
-- -----------------------------------------

INSERT INTO categorias_servicios (nombre, descripcion, icono, orden, requiere_jornada) VALUES
('Extensiones de Pestanas', 'Extensiones clasicas, volumen, wispy, 5D y cuidados post-extension', 'bi-eye-fill', 10, 1),
('Trat. Corporales con Equipo', 'Criolipólisis, VelaSlim y packs reductores/celulitis', 'bi-lightning-charge', 11, 1);

-- -----------------------------------------
-- Paso 3: Marcar categorias existentes que requieren jornada
-- -----------------------------------------

-- Depilacion (cat 1)
UPDATE categorias_servicios SET requiere_jornada = 1 WHERE id = 1;

-- Peluqueria (cat 8)
UPDATE categorias_servicios SET requiere_jornada = 1 WHERE id = 8;

-- -----------------------------------------
-- Paso 4: Mover servicios de extensiones de pestanas
-- Desde "Cejas y Pestanas" (cat 7) a nueva categoria
-- IDs basados en snapshot de produccion 2026-04-02
-- -----------------------------------------

UPDATE servicios
SET id_categoria = (SELECT id FROM categorias_servicios WHERE nombre = 'Extensiones de Pestanas' LIMIT 1)
WHERE id IN (46, 88, 89, 90, 85, 86);
-- 46: Extensiones CLASICAS
-- 88: Extensiones VOLUMEN TECH
-- 89: Extensiones 5D
-- 90: Extensiones EFECTO WISPY
-- 85: Rehabilitacion Mala Praxis
-- 86: Hidratacion Post Lifting/Laminado

-- -----------------------------------------
-- Paso 5: Mover servicios corporales con equipo
-- Desde "Tratamientos Corporales" (cat 3) a nueva categoria
-- -----------------------------------------

UPDATE servicios
SET id_categoria = (SELECT id FROM categorias_servicios WHERE nombre = 'Trat. Corporales con Equipo' LIMIT 1)
WHERE id IN (38, 36, 96, 97);
-- 38: Criolipólisis Plana
-- 36: VelaSlim-Celulitis
-- 96: PACK REDUCTOR
-- 97: PACK CELULITIS

-- -----------------------------------------
-- Paso 6: Crear tabla jornadas
-- -----------------------------------------

CREATE TABLE jornadas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL DEFAULT '08:00:00',
    hora_fin TIME NOT NULL DEFAULT '20:00:00',
    estado ENUM('activa', 'cancelada') NOT NULL DEFAULT 'activa',
    notas TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categorias_servicios(id) ON DELETE CASCADE,
    UNIQUE KEY uk_categoria_fecha (id_categoria, fecha),
    INDEX idx_fecha_estado (fecha, estado),
    INDEX idx_categoria (id_categoria)
) ENGINE=InnoDB;
