-- =============================================
-- Migration 004: Servicios reales de Piel Morena
-- Basado en docs/negocio.md — catálogo real del salón
-- =============================================
-- NOTA: Precios en $0 son placeholder — el admin los configura
-- desde el panel cuando Mari confirme los precios reales.
-- =============================================

-- -----------------------------------------
-- Paso 1: Agregar categorías faltantes
-- Actuales: Depilación(1), Trat.Faciales(2), Trat.Corporales(3), Trat.Frío(4), Maquillaje(5), Uñas(6)
-- Nuevas:   Cejas y Pestañas(7), Peluquería(8), Masajes(9)
-- Se renombra "Uñas" → "Manicuría" para alinear con negocio
-- -----------------------------------------

UPDATE categorias_servicios SET nombre = 'Manicuría', descripcion = 'Kapping, soft gel, semipermanente y más' WHERE id = 6;

INSERT INTO categorias_servicios (nombre, descripcion, icono, orden) VALUES
('Cejas y Pestañas', 'Lifting, tinte, perfilado, laminado y extensiones', 'bi-eye', 7),
('Peluquería', 'Corte, alisado, nanoplastia, keratina y tratamientos capilares', 'bi-scissors', 8),
('Masajes', 'Masajes relajantes y descontracturantes', 'bi-hand-index-thumb', 9);

-- -----------------------------------------
-- Paso 2: Desactivar servicios de ejemplo anteriores
-- (no los borramos para no romper citas existentes)
-- -----------------------------------------

UPDATE servicios SET activo = 0 WHERE id BETWEEN 1 AND 14;

-- -----------------------------------------
-- Paso 3: Insertar servicios reales
-- Organización: cada servicio es individual con su categoría
-- -----------------------------------------

-- ── Depilación (cat 1) ──
INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion_minutos) VALUES
(1, 'Depilación Láser Soprano — Axilas', 'Depilación definitiva con tecnología Soprano. Sesión para zona de axilas.', 0.00, 30),
(1, 'Depilación Láser Soprano — Bikini', 'Depilación definitiva con tecnología Soprano. Sesión para zona bikini.', 0.00, 30),
(1, 'Depilación Láser Soprano — Piernas Completas', 'Depilación definitiva con tecnología Soprano. Sesión para piernas completas.', 0.00, 60),
(1, 'Depilación Láser Soprano — Bozo', 'Depilación definitiva con tecnología Soprano. Sesión para zona de bozo.', 0.00, 15),
(1, 'Depilación Láser Soprano — Brazos', 'Depilación definitiva con tecnología Soprano. Sesión para brazos completos.', 0.00, 45);

-- ── Tratamientos Faciales (cat 2) ──
INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion_minutos) VALUES
(2, 'Limpieza Facial Profunda', 'Limpieza completa con extracción, vapor de ozono y mascarilla hidratante. Ideal para todo tipo de piel.', 0.00, 90),
(2, 'Punta de Diamante', 'Microexfoliación con punta de diamante para renovar la capa superficial de la piel y estimular la producción de colágeno.', 0.00, 60),
(2, 'Dermaplaning', 'Exfoliación mecánica que elimina vello fino y células muertas, dejando la piel suave y luminosa.', 0.00, 60),
(2, 'Limpieza Facial Anti-Age', 'Tratamiento facial enfocado en signos de envejecimiento. Incluye activos reafirmantes y antioxidantes.', 0.00, 90),
(2, 'Limpieza Facial Control Acné', 'Limpieza profunda con activos antibacterianos y seborreguladores para pieles con tendencia acneica.', 0.00, 90),
(2, 'Limpieza Facial Despigmentante', 'Tratamiento con agentes despigmentantes para reducir manchas y unificar el tono de la piel.', 0.00, 90),
(2, 'Limpieza Facial Hidratante', 'Hidratación profunda con ácido hialurónico y vitaminas. Ideal para pieles secas o deshidratadas.', 0.00, 90),
(2, 'Radiofrecuencia Facial', 'Estimulación de colágeno y elastina con energía de radiofrecuencia. Efecto tensor y rejuvenecedor.', 0.00, 60),
(2, 'Peeling Químico', 'Aplicación de ácidos controlados para renovación celular, mejora de textura y luminosidad.', 0.00, 60),
(2, 'Peeling Enzimático', 'Peeling suave con enzimas naturales. Ideal para pieles sensibles que necesitan renovación.', 0.00, 60),
(2, 'Dermapen', 'Microagujas para estimular la regeneración natural de la piel. Mejora cicatrices, poros y textura.', 0.00, 60);

-- ── Tratamientos Corporales (cat 3) ──
INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion_minutos) VALUES
(3, 'Radiofrecuencia Corporal', 'Tratamiento reafirmante corporal con radiofrecuencia. Estimula colágeno y mejora la flacidez.', 0.00, 30),
(3, 'VelaSlim — Celulitis', 'Tratamiento combinado para reducir celulitis con tecnología VelaSlim. Masaje profundo y succión.', 0.00, 60),
(3, 'Electrodos', 'Electroestimulación muscular para tonificar y reafirmar zonas específicas del cuerpo.', 0.00, 30);

-- ── Tratamientos de Frío (cat 4) ──
INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion_minutos) VALUES
(4, 'Criolipólisis en Frío', 'Eliminación de grasa localizada mediante aplicación controlada de frío. Resultados progresivos.', 0.00, 60);

-- ── Manicuría (cat 6) ──
INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion_minutos) VALUES
(6, 'Kapping', 'Técnica de uñas con polvo acrílico y monómero. Cobertura resistente y natural.', 0.00, 120),
(6, 'Soft Gel', 'Aplicación de gel blando para uñas con acabado natural y flexible.', 0.00, 120),
(6, 'Semipermanente', 'Esmaltado semipermanente con secado UV. Duración de 2 a 3 semanas.', 0.00, 90);

-- ── Cejas y Pestañas (cat 7) ──
INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion_minutos) VALUES
(7, 'Lifting de Pestañas', 'Curvado permanente de pestañas naturales con efecto de mayor longitud y apertura.', 0.00, 60),
(7, 'Tinte de Cejas', 'Coloración de cejas para definir la mirada y dar estructura al rostro.', 0.00, 30),
(7, 'Perfilado de Cejas', 'Diseño y depilación de cejas para lograr la forma ideal según la estructura facial.', 0.00, 30),
(7, 'Laminado de Cejas', 'Alisado y fijación de cejas con efecto peinado, dando volumen y dirección.', 0.00, 45),
(7, 'Extensiones de Pestañas', 'Aplicación pelo a pelo o en abanico para pestañas más largas y voluminosas. Solo miércoles.', 0.00, 90);

-- ── Peluquería (cat 8) ──
INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion_minutos) VALUES
(8, 'Corte de Puntas', 'Corte de puntas para sanear el cabello y mantener el largo.', 0.00, 60),
(8, 'Células Madre Capilar', 'Tratamiento reparador con células madre vegetales. Nutre y fortalece el cabello dañado.', 0.00, 60),
(8, 'Nanoplastia', 'Alisado orgánico con nanotecnología. Reduce el volumen y da brillo sin formol.', 0.00, 120),
(8, 'Alisado', 'Alisado profesional para cabello liso y manejable. Duración según largo del cabello.', 0.00, 180),
(8, 'Shock de Keratina', 'Reparación intensiva con keratina para cabello dañado. Restaura brillo y suavidad.', 0.00, 60),
(8, 'Botox Capilar', 'Tratamiento de relleno capilar que repara la fibra del cabello desde adentro.', 0.00, 90),
(8, 'Máscara Reparadora', 'Mascarilla profesional de reparación profunda. Hidrata, nutre y sella las cutículas.', 0.00, 45),
(8, 'Máscara Matizadora', 'Mascarilla con pigmentos para neutralizar tonos no deseados (amarillos/naranjas).', 0.00, 45),
(8, 'Nutrición Capilar', 'Tratamiento nutritivo para devolver vitalidad al cabello reseco y sin brillo.', 0.00, 60);

-- ── Masajes (cat 9) ──
INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion_minutos) VALUES
(9, 'Masaje Relajante — Cuerpo Completo', 'Masaje de relajación con aceites esenciales. Alivia el estrés y relaja la musculatura.', 0.00, 60),
(9, 'Masaje Relajante — Zona Específica', 'Masaje relajante focalizado en una zona específica (espalda, piernas, cervical).', 0.00, 30),
(9, 'Masaje Descontracturante — Cuerpo Completo', 'Masaje profundo para liberar contracturas y nudos musculares en todo el cuerpo.', 0.00, 60),
(9, 'Masaje Descontracturante — Zona Específica', 'Masaje profundo focalizado en zona de tensión (espalda, cuello, hombros).', 0.00, 30);

-- -----------------------------------------
-- Paso 4: Actualizar configuración del negocio
-- -----------------------------------------

UPDATE configuracion SET valor = 'Piel Morena Estética' WHERE clave = 'nombre_negocio';
UPDATE configuracion SET valor = '3624254052' WHERE clave = 'telefono';
UPDATE configuracion SET valor = 'zudaire83@gmail.com' WHERE clave = 'email';
UPDATE configuracion SET valor = 'Vedia 459, Resistencia, Chaco' WHERE clave = 'direccion';
UPDATE configuracion SET valor = '08:00' WHERE clave = 'horario_apertura';
UPDATE configuracion SET valor = 'ARS' WHERE clave = 'moneda';
