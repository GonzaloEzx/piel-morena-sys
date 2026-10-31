-- =============================================
-- Datos de demo LOCAL para probar la agenda por profesional (v2.0).
-- No correr en produccion: ahi los horarios y servicios por profesional
-- los carga Mari desde el panel.
--
-- Repetible: borra y recrea las profesionales @demo.local y sus datos.
-- Fechas relativas a hoy.
-- =============================================

DELETE c FROM citas c JOIN usuarios u ON c.id_empleado = u.id WHERE u.email LIKE '%@demo.local';
DELETE FROM citas WHERE notas = 'demo-v2';
DELETE FROM usuarios WHERE email LIKE '%@demo.local';

INSERT INTO usuarios (nombre, apellidos, email, password, rol, activo) VALUES
 ('Mariangeles', 'Zudaire',   'mari@demo.local',     '', 'empleado', 1),
 ('Rosario',     'Prieto',    'rosario@demo.local',  '', 'empleado', 1),
 ('Lucia',       'Soto',      'lucia@demo.local',    '', 'empleado', 1),
 ('Nathalia',    'Gomez',     'nathalia@demo.local', '', 'empleado', 1),
 ('Nuria',       'Morinigo',  'nuria@demo.local',    '', 'empleado', 1),
 ('Naila',       'Centurion', 'naila@demo.local',    '', 'empleado', 1);

-- Servicios por profesional, segun categoria
INSERT INTO empleados_servicios (id_empleado, id_servicio)
SELECT u.id, s.id FROM usuarios u JOIN servicios s ON s.activo = 1
WHERE (u.email = 'mari@demo.local'     AND s.id_categoria IN (1, 2, 3, 4, 11))
   OR (u.email = 'rosario@demo.local'  AND s.id_categoria = 6)
   OR (u.email = 'lucia@demo.local'    AND s.id_categoria IN (2, 7))
   OR (u.email = 'nathalia@demo.local' AND s.id_categoria = 8)
   OR (u.email = 'nuria@demo.local'    AND s.id_categoria IN (3, 9))
   OR (u.email = 'naila@demo.local'    AND s.id_categoria IN (7, 10));

-- Horarios: lunes a viernes 08-20; Naila solo miercoles; Nuria martes y jueves 14-20
INSERT INTO horarios (id_empleado, dia_semana, hora_inicio, hora_fin, activo)
SELECT u.id, d.dia, '08:00', '20:00', 1
FROM usuarios u JOIN (SELECT 1 dia UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5) d
WHERE u.email IN ('mari@demo.local', 'rosario@demo.local', 'lucia@demo.local', 'nathalia@demo.local');

INSERT INTO horarios (id_empleado, dia_semana, hora_inicio, hora_fin, activo)
SELECT id, 3, '08:00', '20:00', 1 FROM usuarios WHERE email = 'naila@demo.local';

INSERT INTO horarios (id_empleado, dia_semana, hora_inicio, hora_fin, activo)
SELECT u.id, d.dia, '14:00', '20:00', 1
FROM usuarios u JOIN (SELECT 2 dia UNION SELECT 4) d WHERE u.email = 'nuria@demo.local';

-- Clientas de demo
DELETE FROM usuarios WHERE email LIKE '%@clienta.demo';
INSERT INTO usuarios (nombre, apellidos, email, password, telefono, telefono_norm, rol) VALUES
 ('Carla', 'Demo', 'carla@clienta.demo', '', '3624 111111', '3624111111', 'cliente'),
 ('Sofia', 'Demo', 'sofia@clienta.demo', '', '3624 222222', '3624222222', 'cliente');

-- Citas de demo para el proximo dia habil
SET @dia := DATE_ADD(CURDATE(), INTERVAL (CASE DAYOFWEEK(CURDATE() + INTERVAL 1 DAY) WHEN 7 THEN 3 WHEN 1 THEN 2 ELSE 1 END) DAY);

INSERT INTO citas (id_cliente, id_servicio, id_empleado, fecha, hora_inicio, hora_fin, estado, notas)
SELECT (SELECT id FROM usuarios WHERE email = 'carla@clienta.demo'),
       (SELECT es.id_servicio FROM empleados_servicios es JOIN usuarios u ON u.id = es.id_empleado WHERE u.email = 'rosario@demo.local' LIMIT 1),
       (SELECT id FROM usuarios WHERE email = 'rosario@demo.local'),
       @dia, '10:00', '11:00', 'confirmada', 'demo-v2';

INSERT INTO citas (id_cliente, id_servicio, id_empleado, fecha, hora_inicio, hora_fin, estado, notas)
SELECT (SELECT id FROM usuarios WHERE email = 'sofia@clienta.demo'),
       (SELECT es.id_servicio FROM empleados_servicios es JOIN usuarios u ON u.id = es.id_empleado WHERE u.email = 'nathalia@demo.local' LIMIT 1),
       NULL, @dia, '10:00', '11:00', 'pendiente', 'demo-v2';

-- Servicios destacados en la landing (una tarjeta por categoria, hasta 6)
UPDATE servicios s
JOIN (SELECT MIN(id) id FROM servicios WHERE activo = 1 AND id_maquina IS NULL GROUP BY id_categoria ORDER BY MIN(id) LIMIT 6) d ON d.id = s.id
SET s.destacado = 1;
