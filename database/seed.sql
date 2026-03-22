-- =============================================
-- PIEL MORENA - Datos Iniciales (Seed)
-- =============================================

USE piel_morena;

-- -----------------------------------------
-- Admin por defecto (password: admin123 - CAMBIAR EN PRODUCCIÓN)
-- Hash generado con password_hash('admin123', PASSWORD_DEFAULT)
-- -----------------------------------------
INSERT INTO usuarios (nombre, apellidos, email, password, telefono, rol) VALUES
('Admin', 'Piel Morena', 'admin@pielmorena.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+00 000 000 0000', 'admin');

-- -----------------------------------------
-- Categorías de servicios
-- -----------------------------------------
INSERT INTO categorias_servicios (nombre, descripcion, icono, orden) VALUES
('Depilación', 'Servicios de depilación con diferentes técnicas', 'bi-stars', 1),
('Tratamientos Faciales', 'Tratamientos de belleza para el rostro', 'bi-droplet-half', 2),
('Tratamientos Corporales', 'Tratamientos estéticos corporales', 'bi-body-text', 3),
('Tratamientos de Frío', 'Crioterapia y tratamientos con frío', 'bi-snow', 4),
('Maquillaje', 'Servicios de maquillaje profesional', 'bi-palette', 5),
('Uñas', 'Manicure, pedicure y nail art', 'bi-brush', 6);

-- -----------------------------------------
-- Servicios de ejemplo
-- -----------------------------------------
INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion_minutos) VALUES
(1, 'Depilación Láser Axilas', 'Depilación láser de última generación para axilas. Resultados duraderos y piel suave.', 350.00, 30),
(1, 'Depilación Láser Piernas Completas', 'Depilación láser completa de piernas. Tecnología avanzada para todo tipo de piel.', 1200.00, 60),
(1, 'Depilación con Cera Bikini', 'Depilación con cera caliente zona bikini. Resultados limpios y duraderos.', 250.00, 30),
(2, 'Limpieza Facial Profunda', 'Limpieza facial completa con extracción, vapor y mascarilla hidratante.', 450.00, 60),
(2, 'Hidratación Facial', 'Tratamiento de hidratación profunda con ácido hialurónico.', 550.00, 45),
(2, 'Microdermoabrasión', 'Exfoliación profunda para renovar la piel y reducir manchas.', 650.00, 45),
(3, 'Masaje Relajante', 'Masaje corporal relajante con aceites esenciales aromáticos.', 500.00, 60),
(3, 'Reducción de Medidas', 'Tratamiento corporal para reducción de medidas con tecnología.', 800.00, 60),
(4, 'Crioterapia Facial', 'Tratamiento con frío para rejuvenecimiento y tonificación facial.', 700.00, 30),
(4, 'Criolipolisis', 'Eliminación de grasa localizada con tecnología de frío controlado.', 1500.00, 60),
(5, 'Maquillaje Social', 'Maquillaje profesional para eventos sociales y ocasiones especiales.', 400.00, 45),
(5, 'Maquillaje de Novia', 'Maquillaje profesional para novias con prueba previa incluida.', 800.00, 90),
(6, 'Manicure Spa', 'Manicure completo con exfoliación, hidratación y esmaltado.', 200.00, 45),
(6, 'Pedicure Spa', 'Pedicure completo con tratamiento de pies y esmaltado.', 250.00, 60);

-- -----------------------------------------
-- Configuración inicial
-- -----------------------------------------
INSERT INTO configuracion (clave, valor, descripcion) VALUES
('nombre_negocio', 'Piel Morena', 'Nombre del salón'),
('telefono', '+00 000 000 0000', 'Teléfono principal'),
('email', 'contacto@pielmorena.com', 'Email de contacto'),
('direccion', 'Dirección del salón', 'Dirección física'),
('horario_apertura', '09:00', 'Hora de apertura'),
('horario_cierre', '20:00', 'Hora de cierre'),
('dias_laborales', '1,2,3,4,5,6', 'Días laborales (1=Lun, 7=Dom)'),
('moneda', 'MXN', 'Moneda del sistema'),
('intervalo_citas', '30', 'Intervalo en minutos entre citas'),
('color_primario', '#8A7650', 'Color primario del tema'),
('color_secundario', '#FFE1AF', 'Color secundario del tema');

