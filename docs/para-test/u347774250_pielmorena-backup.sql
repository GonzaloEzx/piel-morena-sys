-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 02-04-2026 a las 03:01:12
-- Versión del servidor: 11.8.6-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u347774250_pielmorena`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id` int(11) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `duracion_minutos` int(11) NOT NULL DEFAULT 30,
  `imagen` varchar(255) DEFAULT NULL,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `banner` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id`, `id_categoria`, `nombre`, `descripcion`, `precio`, `duracion_minutos`, `imagen`, `destacado`, `banner`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Depilación Láser ADDS', 'Depilación láser de última generación combo completo. Resultados duraderos y piel suave.', 26000.00, 60, NULL, 0, NULL, 0, '2026-03-08 05:06:37', '2026-03-30 23:57:01'),
(2, 1, 'Depilación Láser Piernas Completas', 'Depilación láser completa de piernas. Tecnología avanzada para todo tipo de piel.', 1200.00, 60, NULL, 0, NULL, 0, '2026-03-08 05:06:37', '2026-03-28 21:13:40'),
(3, 1, 'Depilación con Cera Bikini', 'Depilación con cera caliente zona bikini. Resultados limpios y duraderos.', 250.00, 30, NULL, 0, NULL, 0, '2026-03-08 05:06:37', '2026-03-28 21:13:40'),
(4, 2, 'Limpieza Facial Profunda', 'Limpieza facial completa con extracción, vapor y mascarilla hidratante.', 450.00, 60, NULL, 0, NULL, 0, '2026-03-08 05:06:37', '2026-03-28 21:13:40'),
(5, 2, 'Hidratación Facial', 'Tratamiento de hidratación profunda con ácido hialurónico.', 550.00, 45, NULL, 0, NULL, 0, '2026-03-08 05:06:37', '2026-03-28 21:13:40'),
(6, 2, 'Microdermoabrasión', 'Exfoliación profunda para renovar la piel y reducir manchas.', 650.00, 45, NULL, 0, NULL, 0, '2026-03-08 05:06:37', '2026-03-28 21:13:40'),
(7, 3, 'Masaje Relajante', 'Masaje corporal relajante con aceites esenciales aromáticos.', 500.00, 60, NULL, 0, NULL, 0, '2026-03-08 05:06:37', '2026-03-28 21:13:40'),
(8, 3, 'Reducción de Medidas', 'Tratamiento corporal para reducción de medidas con tecnología.', 800.00, 60, NULL, 0, NULL, 0, '2026-03-08 05:06:37', '2026-03-28 21:13:40'),
(9, 4, 'Crioterapia Facial', 'Tratamiento con frío para rejuvenecimiento y tonificación facial.', 700.00, 30, NULL, 0, NULL, 0, '2026-03-08 05:06:37', '2026-03-28 21:13:40'),
(10, 4, 'Criolipolisis', 'Eliminación de grasa localizada con tecnología de frío controlado.', 1500.00, 60, NULL, 0, NULL, 0, '2026-03-08 05:06:37', '2026-03-28 21:13:40'),
(11, 5, 'Maquillaje Social', 'Maquillaje profesional para eventos sociales y ocasiones especiales.', 400.00, 45, NULL, 0, NULL, 0, '2026-03-08 05:06:37', '2026-03-28 21:13:40'),
(12, 5, 'Maquillaje de Novia', 'Maquillaje profesional para novias con prueba previa incluida.', 800.00, 90, NULL, 0, NULL, 0, '2026-03-08 05:06:37', '2026-03-28 21:13:40'),
(13, 6, 'Manicure Spa', 'Manicure completo con exfoliación, hidratación y esmaltado.', 200.00, 45, NULL, 0, NULL, 0, '2026-03-08 05:06:37', '2026-03-28 21:13:40'),
(14, 6, 'Pedicure Spa', 'Pedicure completo con tratamiento de pies y esmaltado.', 250.00, 60, NULL, 0, NULL, 0, '2026-03-08 05:06:37', '2026-03-28 21:13:40'),
(15, 2, 'Hydra Glow Facial', 'Hidratación premium para rostro re caro', 14450.00, 50, NULL, 0, NULL, 0, '2026-03-08 05:22:52', '2026-03-28 22:52:14'),
(16, 7, 'Depilación de pelo de nariz', 'Poda de los pelos de la nariz', 6000.00, 45, NULL, 0, NULL, 0, '2026-03-15 10:31:38', '2026-03-28 22:52:14'),
(17, 1, 'QA Test Service - BORRAR', 'Servicio de prueba QA, eliminar después', 999.00, 15, NULL, 0, NULL, 0, '2026-03-22 07:25:35', '2026-03-22 07:26:48'),
(18, 6, 'Manicura', '', 14000.00, 30, NULL, 0, NULL, 0, '2026-03-26 22:59:08', '2026-04-02 00:06:29'),
(19, 1, 'Depilación Definitiva | Método ADSS — Axilas', 'Depilación definitiva con tecnología Soprano. Sesión para zona de axilas.', 10000.00, 30, NULL, 1, NULL, 0, '2026-03-28 21:13:40', '2026-04-01 00:08:38'),
(20, 1, 'Depilación Láser Soprano — Bikini', 'Depilación definitiva con tecnología Soprano. Sesión para zona bikini.', 400.00, 30, NULL, 0, NULL, 0, '2026-03-28 21:13:40', '2026-04-01 23:27:50'),
(21, 1, 'Depilación Láser Soprano — Piernas Completas', 'Depilación definitiva con tecnología Soprano. Sesión para piernas completas.', 300.00, 60, NULL, 0, NULL, 0, '2026-03-28 21:13:40', '2026-04-01 23:27:47'),
(22, 1, 'Depilación Láser Soprano — Bozo', 'Depilación definitiva con tecnología Soprano. Sesión para zona de bozo.', 200.00, 15, NULL, 0, NULL, 0, '2026-03-28 21:13:40', '2026-04-01 23:27:40'),
(23, 1, 'Depilación Láser Soprano — Brazos', 'Depilación definitiva con tecnología Soprano. Sesión para brazos completos.', 100.00, 45, NULL, 0, NULL, 0, '2026-03-28 21:13:40', '2026-04-01 23:27:37'),
(24, 2, 'Limpieza Facial Profunda', 'Limpieza, exfoliación, extracción de puntos negros para una piel mas saludable y luminosa, protocolo adaptado al tipo de piel.', 25000.00, 90, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:33:28'),
(25, 2, 'Punta de Diamante', 'Microdermoabrasión con punta de diamante para renovar la capa superficial de la piel y eliminar células muertas.', 22000.00, 90, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:31:31'),
(26, 2, 'Dermaplaning', 'Exfoliación mecánica que elimina vello fino y células muertas, dejando la piel suave y luminosa. Activos + Mascaras+ Aparatologia', 30000.00, 60, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:32:09'),
(27, 2, 'Limpieza Facial Anti-Age', 'Tratamiento facial enfocado en signos de envejecimiento. Incluye activos reafirmantes y antioxidantes.', 32000.00, 90, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:32:23'),
(28, 2, 'Limpieza Facial Control Acné', 'Limpieza profunda con activos, aparatología y sebo reguladores para pieles con tendencia acneica.', 32000.00, 90, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:34:02'),
(29, 2, 'Limpieza Facial Despigmentante', 'Tratamiento con agentes despigmentantes para reducir manchas y unificar el tono de la piel.', 32000.00, 90, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:32:35'),
(30, 2, 'Limpieza Facial Hidratante', 'Hidratación profunda con ácido hialurónico y vitaminas. Ideal para pieles secas o deshidratadas.', 0.00, 90, NULL, 0, NULL, 0, '2026-03-28 21:13:40', '2026-04-02 00:34:13'),
(31, 2, 'Radiofrecuencia Facial', 'Estimulación de colágeno y elastina con energía de radiofrecuencia. Efecto tensor y rejuvenecedor.', 25000.00, 90, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:34:34'),
(32, 2, 'Peeling Químico', 'Aplicación de ácidos controlados para renovación celular, mejora de textura y luminosidad.', 30000.00, 90, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:35:07'),
(33, 2, 'Peeling Enzimático', 'Peeling suave con enzimas naturales. Ideal para pieles sensibles que necesitan renovación.', 28000.00, 90, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:34:50'),
(34, 2, 'Dermapen', 'Microagujas para estimular la regeneración natural de la piel. Mejora cicatrices, poros y textura. Tratamiento completo y personalizado.', 45000.00, 60, NULL, 1, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:35:30'),
(35, 3, 'Radiofrecuencia Corporal', 'Tratamiento reafirmante corporal con radiofrecuencia. Estimula colágeno y mejora la flacidez. Sesiones semanales con seguimiento.', 10000.00, 30, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:24:05'),
(36, 3, 'VelaSlim-Celulitis', 'Combina 4 tecnologías, radiofrecuencia, rodillos, vacum y laser infrarrojo, para mejorar el drenaje linfático, eliminando celulitis, adiposidades y grasa localizada. Sesiones cada 15 días.', 16000.00, 60, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:26:10'),
(37, 3, 'Electrodos', 'Electroestimulación muscular para tonificar y reafirmar zonas específicas del cuerpo. Función para celulitis y reducir. Sesiones 3 veces por semana.', 5000.00, 30, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:26:54'),
(38, 3, 'Criolipólisis Plana', 'Eliminación de grasa localizada mediante aplicación controlada de frío. Resultados progresivos. Una sesión por mes.', 18000.00, 60, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:29:44'),
(39, 6, 'Kapping', 'Refuerzo sobre la uña natural con gel que aporta resistencia y durabilidad. Ideal para quienes buscan mantener el largo natural evitando quiebres, con acabado prolijo, brillante y elegante.', 22000.00, 140, 'https://pielmorenaestetica.com.ar/uploads/servicios/servicios_1775088018_56d344d8.jpg', 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:04:41'),
(40, 6, 'Soft Gel', 'Sistema de extensión con tips de gel que brinda uñas mas largas, livianas y naturales. Cómodas, resistentes y con terminación perfecta. No incluye diseño.', 25000.00, 140, 'https://pielmorenaestetica.com.ar/uploads/servicios/servicios_1775087939_0e61e0e2.jpg', 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:03:39'),
(41, 6, 'Semipermanente', 'Esmaltado de larga duración que mantiene el brillo y el color por semanas. Secado inmediato, sin desgaste, ideal para lucir uñas perfectas todos los días. No incluye diseño.', 20000.00, 120, 'https://pielmorenaestetica.com.ar/uploads/servicios/servicios_1775088039_cb668a9e.jpg', 1, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:05:05'),
(42, 9, 'Lifting de Pestañas Nutritivo', 'Curva las pestañas dejándolas arqueadas. Incluye hidratación, nutrición y tinte negro para brindar acabado delicado. Durabilidad de 2 meses.', 24000.00, 75, NULL, 1, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:38:50'),
(43, 9, 'Tinte/Henna CEJAS', 'Aporta color al pelo y la piel brindando un efecto relleno, ayuda a  mejorar la simetría. Mirada definida, natural y duradera. Efecto maquillaje, duración 2-3 semanas.', 20000.00, 30, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-01 23:55:52'),
(44, 9, 'Diseño y Depilacion de Cejas', 'Diseño personalizado de cejas que armoniza el rostro brindando un acabado natural acorde a la simetría facial.', 16000.00, 30, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-01 23:54:23'),
(45, 9, 'Laminado de Cejas', 'Lamina el pelo de las cejas dejándolos maleables para darle el direccionamiento que gustes, dando un efecto de mayor volumen y simetría. Incluye nutrición, diseño y depilación, tinte a elección. Durabilidad de 1 mes.', 26000.00, 60, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-01 23:58:28'),
(46, 9, 'Extensiones de Pestañas CLASICAS', 'Solo miércoles.', 26000.00, 90, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:12:47'),
(47, 10, 'Corte de Puntas', 'Corte de puntas para sanear el cabello y mantener el largo.', 7000.00, 15, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:18:49'),
(48, 10, 'Células Madre Capilar', 'Tratamiento reparador con células madre vegetales. Nutre y fortalece el cabello dañado.', 32000.00, 60, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:18:33'),
(49, 10, 'Nanoplastia', 'Alisado orgánico con nanotecnología. Reduce el volumen y da brillo sin formol.', 38000.00, 120, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:18:14'),
(50, 10, 'Alisado SIN FORMOL', 'Alisado profesional para cabello liso y manejable. Duración según largo del cabello.', 42000.00, 180, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:17:55'),
(51, 10, 'Shock de Keratina', 'Reparación intensiva con keratina para cabello dañado. Restaura brillo y suavidad.', 35000.00, 120, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:17:16'),
(52, 10, 'Botox PREMIUM', 'Tratamiento de relleno capilar que repara la fibra del cabello desde adentro.', 35000.00, 120, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:16:55'),
(53, 10, 'Máscara Reparadora', 'Mascarilla profesional de reparación profunda. Hidrata, nutre y sella las cutículas.', 30000.00, 90, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:16:21'),
(54, 10, 'Máscara Matizadora', 'Mascarilla con pigmentos para neutralizar tonos no deseados (amarillos/naranjas).', 28000.00, 90, 'https://pielmorenaestetica.com.ar/uploads/servicios/servicios_1774923717_38fb002d.png', 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:29:53'),
(55, 10, 'Nutrición Capilar', 'Tratamiento nutritivo para devolver vitalidad al cabello reseco y sin brillo.', 28000.00, 90, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:15:33'),
(56, 11, 'Masaje Relajante — Cuerpo Completo', 'Masaje de relajación con aceites esenciales. Alivia el estrés y relaja la musculatura.', 300.00, 20, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:29:59'),
(57, 11, 'Masaje Relajante — Zona Específica', 'Masaje relajante focalizado en una zona específica (espalda, piernas, cervical).', 1000.00, 160, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:30:05'),
(58, 11, 'Masaje Descontracturante — Cuerpo Completo', 'Masaje profundo para liberar contracturas y nudos musculares en todo el cuerpo.', 1000.00, 120, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:30:11'),
(59, 11, 'Masaje Descontracturante — Zona Específica', 'Masaje profundo focalizado en zona de tensión (espalda, cuello, hombros).', 12000.00, 30, NULL, 0, NULL, 1, '2026-03-28 21:13:40', '2026-04-02 00:04:56'),
(60, 11, 'nuevo servicio', '', 123.00, 30, NULL, 0, NULL, 0, '2026-04-01 00:10:04', '2026-04-01 01:25:00'),
(61, 1, 'COMBO 1', 'Piernas completas + Cavado completo + Axilas', 20500.00, 30, NULL, 0, NULL, 0, '2026-04-01 23:24:16', '2026-04-01 23:24:25'),
(62, 1, 'COMBO 1', 'Piernas completas + Cavado completo + Axilas', 20500.00, 30, NULL, 0, NULL, 0, '2026-04-01 23:24:16', '2026-04-01 23:27:43'),
(63, 1, 'COMBO 1', 'Piernas completas + Cavado completo + Axilas', 20500.00, 30, NULL, 0, NULL, 0, '2026-04-01 23:24:16', '2026-04-01 23:24:36'),
(64, 1, 'COMBO 1: Piernas completas + Cavado C + Axilas', 'Depilación Laser con tecnología ADSS adaptada a todos los tipos de piel, indoloro, resultados desde la 1er sesión.', 20500.00, 30, NULL, 1, NULL, 1, '2026-04-01 23:27:24', '2026-04-02 00:37:05'),
(65, 1, 'COMBO 2: Piernas completas + Bozo + Cavado C + Axilas', '', 21500.00, 30, NULL, 0, NULL, 1, '2026-04-01 23:28:46', '2026-04-01 23:28:46'),
(66, 1, 'COMBO 3: Piernas C + Rostro C + Axilas + Cavado C + Tiro de C + Abdomen + Glúteos + Espalda', 'No incluye brazos', 26000.00, 60, NULL, 0, NULL, 1, '2026-04-01 23:31:05', '2026-04-01 23:32:06'),
(67, 1, 'COMBO 4: Cavado completo + Axilas + Tiro de cola', '', 18000.00, 20, NULL, 0, NULL, 1, '2026-04-01 23:31:55', '2026-04-01 23:31:55'),
(68, 1, 'Axilas', '', 10000.00, 10, NULL, 0, NULL, 1, '2026-04-01 23:37:07', '2026-04-01 23:37:07'),
(69, 1, 'Cavado completo', '', 10000.00, 10, NULL, 0, NULL, 1, '2026-04-01 23:37:27', '2026-04-01 23:37:27'),
(70, NULL, 'Zona bikini', '', 8000.00, 10, NULL, 0, NULL, 1, '2026-04-01 23:37:43', '2026-04-01 23:37:43'),
(71, 1, 'Piernas completas', '', 15000.00, 25, NULL, 0, NULL, 1, '2026-04-01 23:38:01', '2026-04-01 23:38:32'),
(72, 1, 'Media pierna', '', 10000.00, 15, NULL, 0, NULL, 1, '2026-04-01 23:38:23', '2026-04-01 23:38:23'),
(73, 1, 'Rostro completo', '', 10000.00, 10, NULL, 0, NULL, 1, '2026-04-01 23:38:54', '2026-04-01 23:38:54'),
(74, 1, 'Bozo', '', 6000.00, 10, NULL, 0, NULL, 1, '2026-04-01 23:39:10', '2026-04-01 23:39:10'),
(75, 1, 'Brazos completos', '', 15000.00, 25, NULL, 0, NULL, 1, '2026-04-01 23:39:35', '2026-04-01 23:39:35'),
(76, 1, 'Tiro de cola', '', 8000.00, 30, NULL, 0, NULL, 1, '2026-04-01 23:39:48', '2026-04-01 23:39:48'),
(77, 1, 'Tiro de cola', '', 8000.00, 10, NULL, 0, NULL, 1, '2026-04-01 23:39:48', '2026-04-01 23:39:54'),
(78, 1, 'Gluteos', '', 10000.00, 10, NULL, 0, NULL, 1, '2026-04-01 23:40:14', '2026-04-01 23:40:14'),
(79, 1, 'Abdomen', '', 10000.00, 10, NULL, 0, NULL, 1, '2026-04-01 23:40:31', '2026-04-01 23:40:31'),
(80, 1, 'Linea alba', '', 5000.00, 5, NULL, 0, NULL, 1, '2026-04-01 23:40:50', '2026-04-01 23:40:50'),
(81, 1, 'Manos y pies', '', 6000.00, 10, NULL, 0, NULL, 1, '2026-04-01 23:41:08', '2026-04-01 23:41:15'),
(82, 1, 'Espalda', '', 15000.00, 15, NULL, 0, NULL, 1, '2026-04-01 23:41:35', '2026-04-01 23:41:52'),
(83, 1, 'HOMBRES: Espalda C + Pecho + Abdomen', '', 20000.00, 30, NULL, 0, NULL, 1, '2026-04-01 23:42:19', '2026-04-01 23:42:19'),
(84, 1, 'HOMBRES: Piernas C + Brazos + Pecho', '', 20000.00, 30, NULL, 0, NULL, 1, '2026-04-01 23:42:48', '2026-04-01 23:42:48'),
(85, 9, 'Rehabilitación Mala Praxis', 'Restaura y reestructura el pelo volviéndolo a su estado natural luego de una mala praxis. Se realiza protocolo de hidratación y nutrición profunda.', 20000.00, 45, NULL, 0, NULL, 1, '2026-04-02 00:08:17', '2026-04-02 00:08:17'),
(86, 9, 'Hidratacion Post Lifting/Laminado', 'Fundamental para mantener el servicio y el pelo saludable. Repone, hidrata y nutre el pelo luego del proceso químico y da una mayor durabilidad al servicio.', 18000.00, 45, NULL, 0, NULL, 1, '2026-04-02 00:09:50', '2026-04-02 00:09:50'),
(87, NULL, 'Hidra Lips', 'Hidratación profunda en labios con acido hialuronico que devuelve suavidad, volumen natural y luminosidad. Mejora la textura y previene la resequedad/quiebre/agrietamiento.', 30000.00, 60, NULL, 0, NULL, 1, '2026-04-02 00:11:22', '2026-04-02 00:11:22'),
(88, 9, 'Extensiones de pestañas VOLUMEN TECH', '', 31000.00, 90, NULL, 0, NULL, 1, '2026-04-02 00:13:11', '2026-04-02 00:13:18'),
(89, 9, 'Extensiones de pestañas 5D', '', 32000.00, 90, NULL, 0, NULL, 1, '2026-04-02 00:13:45', '2026-04-02 00:13:45'),
(90, 9, 'Extensiones de pestañas EFECTO WISPY', '', 34000.00, 90, NULL, 0, NULL, 1, '2026-04-02 00:14:17', '2026-04-02 00:14:17'),
(91, 10, 'Shock de Keratina SIN FORMOL', '', 28000.00, 120, NULL, 0, NULL, 1, '2026-04-02 00:19:24', '2026-04-02 00:19:24'),
(92, 10, 'Botox SIN FORMOL', '', 28000.00, 120, NULL, 0, NULL, 1, '2026-04-02 00:19:50', '2026-04-02 00:19:50'),
(93, 10, 'Lavado + Planchado/Ondas', '', 20000.00, 45, NULL, 0, NULL, 1, '2026-04-02 00:20:22', '2026-04-02 00:20:22'),
(94, 10, 'Lavado + Nutrición Express', '', 25000.00, 45, NULL, 1, NULL, 1, '2026-04-02 00:20:48', '2026-04-02 00:38:18'),
(95, 10, 'Alisado Fotonico/Extra fuerte/Chocolate', '', 48000.00, 120, NULL, 0, NULL, 1, '2026-04-02 00:21:32', '2026-04-02 00:21:32'),
(96, 3, 'PACK REDUCTOR', '1 sesión de Criolipolisis, 10 sesiones de electrodos función Reducir + 10 sesiones de Radiofrecuencia corporal.', 50000.00, 60, NULL, 0, NULL, 1, '2026-04-02 00:28:17', '2026-04-02 00:28:17'),
(97, 3, 'PACK CELULITIS', '2 sesiones de Vela Slim Celulitis, 10 sesiones de Electrodos función Reducir, 10 sesiones de Radiofrecuencia corporal.', 50000.00, 60, NULL, 1, NULL, 1, '2026-04-02 00:29:06', '2026-04-02 00:35:58');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria` (`id_categoria`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `idx_destacado` (`destacado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD CONSTRAINT `servicios_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_servicios` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

