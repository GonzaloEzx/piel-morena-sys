-- =============================================
-- Migration 005: Cleanup datos de prueba en producción
-- =============================================
-- Problema: temp_datos_prueba.sql y temp_datos_extra.sql se importaron
-- en producción, creando categorías duplicadas ("Depilacion", "Facial",
-- "Corporal") que colisionaron con los IDs esperados por migration 004.
--
-- Resultado: servicios de Cejas y Pestañas, Peluquería y Masajes quedaron
-- asignados a las categorías de prueba en vez de las reales. Además,
-- servicios de prueba (Hydra Glow Facial, etc.) se mezclaron con los reales.
--
-- Esta migración corrige todo por NOMBRE (no por ID) para ser robusta
-- independientemente del auto-increment real en producción.
-- =============================================

-- -----------------------------------------
-- Paso 1: Desactivar categorías de prueba
-- -----------------------------------------

UPDATE categorias_servicios SET activo = 0
WHERE nombre IN ('Depilacion', 'Facial', 'Corporal');

-- Maquillaje no es un servicio real del negocio
UPDATE categorias_servicios SET activo = 0
WHERE nombre = 'Maquillaje';

-- -----------------------------------------
-- Paso 2: Asegurar que las 8 categorías reales
-- estén activas con orden correcto e iconos
-- -----------------------------------------

UPDATE categorias_servicios
SET activo = 1, orden = 1, icono = 'bi-stars'
WHERE nombre = 'Depilación';

UPDATE categorias_servicios
SET activo = 1, orden = 2, icono = 'bi-droplet-half'
WHERE nombre = 'Tratamientos Faciales';

UPDATE categorias_servicios
SET activo = 1, orden = 3, icono = 'bi-body-text'
WHERE nombre = 'Tratamientos Corporales';

UPDATE categorias_servicios
SET activo = 1, orden = 4, icono = 'bi-snow'
WHERE nombre = 'Tratamientos de Frío';

UPDATE categorias_servicios
SET activo = 1, orden = 5, icono = 'bi-brush'
WHERE nombre = 'Manicuría';

UPDATE categorias_servicios
SET activo = 1, orden = 6, icono = 'bi-eye'
WHERE nombre = 'Cejas y Pestañas';

UPDATE categorias_servicios
SET activo = 1, orden = 7, icono = 'bi-scissors'
WHERE nombre = 'Peluquería';

UPDATE categorias_servicios
SET activo = 1, orden = 8, icono = 'bi-hand-index-thumb'
WHERE nombre = 'Masajes';

-- -----------------------------------------
-- Paso 3: Reasignar servicios reales a sus
-- categorías correctas (por nombre)
-- Esto corrige los que cayeron en categorías
-- de prueba por colisión de IDs
-- -----------------------------------------

-- Depilación
UPDATE servicios SET id_categoria = (
  SELECT id FROM categorias_servicios WHERE nombre = 'Depilación' LIMIT 1
) WHERE nombre LIKE 'Depilación Láser Soprano%';

-- Tratamientos Faciales
UPDATE servicios SET id_categoria = (
  SELECT id FROM categorias_servicios WHERE nombre = 'Tratamientos Faciales' LIMIT 1
) WHERE nombre IN (
  'Punta de Diamante', 'Dermaplaning',
  'Limpieza Facial Anti-Age', 'Limpieza Facial Control Acné',
  'Limpieza Facial Despigmentante', 'Limpieza Facial Hidratante',
  'Radiofrecuencia Facial', 'Peeling Químico', 'Peeling Enzimático', 'Dermapen'
);

-- Limpieza Facial Profunda: reasignar solo la versión real (precio 0)
-- La de seed (deactivada) y la de temp se manejan aparte
UPDATE servicios SET id_categoria = (
  SELECT id FROM categorias_servicios WHERE nombre = 'Tratamientos Faciales' LIMIT 1
) WHERE nombre = 'Limpieza Facial Profunda' AND precio = 0.00;

-- Tratamientos Corporales
UPDATE servicios SET id_categoria = (
  SELECT id FROM categorias_servicios WHERE nombre = 'Tratamientos Corporales' LIMIT 1
) WHERE nombre IN (
  'Radiofrecuencia Corporal', 'VelaSlim — Celulitis', 'Electrodos'
);

-- Tratamientos de Frío
UPDATE servicios SET id_categoria = (
  SELECT id FROM categorias_servicios WHERE nombre = 'Tratamientos de Frío' LIMIT 1
) WHERE nombre = 'Criolipólisis en Frío';

-- Manicuría
UPDATE servicios SET id_categoria = (
  SELECT id FROM categorias_servicios WHERE nombre = 'Manicuría' LIMIT 1
) WHERE nombre IN ('Kapping', 'Soft Gel', 'Semipermanente');

-- Cejas y Pestañas
UPDATE servicios SET id_categoria = (
  SELECT id FROM categorias_servicios WHERE nombre = 'Cejas y Pestañas' LIMIT 1
) WHERE nombre IN (
  'Lifting de Pestañas', 'Tinte de Cejas', 'Perfilado de Cejas',
  'Laminado de Cejas', 'Extensiones de Pestañas'
);

-- Peluquería
UPDATE servicios SET id_categoria = (
  SELECT id FROM categorias_servicios WHERE nombre = 'Peluquería' LIMIT 1
) WHERE nombre IN (
  'Corte de Puntas', 'Células Madre Capilar', 'Nanoplastia', 'Alisado',
  'Shock de Keratina', 'Botox Capilar', 'Máscara Reparadora',
  'Máscara Matizadora', 'Nutrición Capilar'
);

-- Masajes
UPDATE servicios SET id_categoria = (
  SELECT id FROM categorias_servicios WHERE nombre = 'Masajes' LIMIT 1
) WHERE nombre IN (
  'Masaje Relajante — Cuerpo Completo', 'Masaje Relajante — Zona Específica',
  'Masaje Descontracturante — Cuerpo Completo', 'Masaje Descontracturante — Zona Específica'
);

-- -----------------------------------------
-- Paso 4: Desactivar servicios de prueba
-- -----------------------------------------

-- Servicios creados por temp_datos_prueba/extra
UPDATE servicios SET activo = 0
WHERE nombre IN (
  'Hydra Glow Facial',
  'Depilacion Laser Axilas',
  'Masaje Relajante'
);

-- Servicios originales del seed (IDs 1-14, ya desactivados por 004)
-- Reforzar por si alguno quedó activo
UPDATE servicios SET activo = 0
WHERE nombre IN (
  'Depilación Láser Axilas', 'Depilación Láser Piernas Completas',
  'Depilación con Cera Bikini', 'Hidratación Facial', 'Microdermoabrasión',
  'Reducción de Medidas', 'Crioterapia Facial', 'Criolipolisis',
  'Maquillaje Social', 'Maquillaje de Novia', 'Manicure Spa', 'Pedicure Spa'
) AND precio > 0;

-- Limpieza Facial Profunda del seed (tiene precio > 0)
UPDATE servicios SET activo = 0
WHERE nombre = 'Limpieza Facial Profunda' AND precio > 0;

-- Masaje Relajante del seed (tiene precio > 0)
UPDATE servicios SET activo = 0
WHERE nombre = 'Masaje Relajante' AND precio > 0;

-- Desactivar cualquier servicio huérfano en categorías de prueba
UPDATE servicios SET activo = 0
WHERE id_categoria IN (
  SELECT id FROM categorias_servicios WHERE nombre IN ('Depilacion', 'Facial', 'Corporal', 'Maquillaje')
);

-- -----------------------------------------
-- Paso 5: Asegurar que todos los servicios
-- reales estén activos
-- -----------------------------------------

UPDATE servicios SET activo = 1
WHERE nombre IN (
  -- Depilación (5)
  'Depilación Láser Soprano — Axilas',
  'Depilación Láser Soprano — Bikini',
  'Depilación Láser Soprano — Piernas Completas',
  'Depilación Láser Soprano — Bozo',
  'Depilación Láser Soprano — Brazos',
  -- Tratamientos Faciales (11)
  'Limpieza Facial Profunda',
  'Punta de Diamante',
  'Dermaplaning',
  'Limpieza Facial Anti-Age',
  'Limpieza Facial Control Acné',
  'Limpieza Facial Despigmentante',
  'Limpieza Facial Hidratante',
  'Radiofrecuencia Facial',
  'Peeling Químico',
  'Peeling Enzimático',
  'Dermapen',
  -- Tratamientos Corporales (3)
  'Radiofrecuencia Corporal',
  'VelaSlim — Celulitis',
  'Electrodos',
  -- Tratamientos de Frío (1)
  'Criolipólisis en Frío',
  -- Manicuría (3)
  'Kapping',
  'Soft Gel',
  'Semipermanente',
  -- Cejas y Pestañas (5)
  'Lifting de Pestañas',
  'Tinte de Cejas',
  'Perfilado de Cejas',
  'Laminado de Cejas',
  'Extensiones de Pestañas',
  -- Peluquería (9)
  'Corte de Puntas',
  'Células Madre Capilar',
  'Nanoplastia',
  'Alisado',
  'Shock de Keratina',
  'Botox Capilar',
  'Máscara Reparadora',
  'Máscara Matizadora',
  'Nutrición Capilar',
  -- Masajes (4)
  'Masaje Relajante — Cuerpo Completo',
  'Masaje Relajante — Zona Específica',
  'Masaje Descontracturante — Cuerpo Completo',
  'Masaje Descontracturante — Zona Específica'
) AND precio = 0.00;

-- -----------------------------------------
-- Paso 6: Re-aplicar configuración real
-- (temp_datos sobrescribió con ON DUPLICATE KEY)
-- -----------------------------------------

UPDATE configuracion SET valor = 'Piel Morena Estética' WHERE clave = 'nombre_negocio';
UPDATE configuracion SET valor = '3624254052' WHERE clave = 'telefono';
UPDATE configuracion SET valor = 'zudaire83@gmail.com' WHERE clave = 'email';
UPDATE configuracion SET valor = 'Vedia 459, Resistencia, Chaco' WHERE clave = 'direccion';
UPDATE configuracion SET valor = '08:00' WHERE clave = 'horario_apertura';
UPDATE configuracion SET valor = '20:00' WHERE clave = 'horario_cierre';
UPDATE configuracion SET valor = 'ARS' WHERE clave = 'moneda';
UPDATE configuracion SET valor = '1,2,3,4,5' WHERE clave = 'dias_laborales';

-- =============================================
-- Resultado esperado tras esta migración:
--
-- Categorías activas (8):
--   Depilación, Tratamientos Faciales, Tratamientos Corporales,
--   Tratamientos de Frío, Manicuría, Cejas y Pestañas, Peluquería, Masajes
--
-- Servicios activos (41):
--   5 + 11 + 3 + 1 + 3 + 5 + 9 + 4
--
-- Categorías desactivadas:
--   Depilacion, Facial, Corporal, Maquillaje
--
-- Servicios desactivados:
--   Todos los de seed (1-14), Hydra Glow Facial,
--   Depilacion Laser Axilas, Masaje Relajante
-- =============================================
