-- =============================================
-- Datos de demo sobre la base de PRODUCCION (sitio sin uso, autorizado por
-- Gonzalo el 2026-10-31) para probar /test. Usa las profesionales reales y
-- asigna servicios por nombre de categoria. Todo es identificable:
--   empleados_servicios (estaba vacia), horario de Naila, citas 'demo-v2',
--   clientas '@demo.local'.
-- Limpieza: ver el final.
-- =============================================

DELETE FROM citas WHERE notas = 'demo-v2';
DELETE FROM usuarios WHERE email LIKE '%@demo.local';

INSERT IGNORE INTO empleados_servicios (id_empleado, id_servicio)
SELECT u.id, s.id
FROM usuarios u
JOIN servicios s ON s.activo = 1
JOIN categorias_servicios c ON c.id = s.id_categoria
WHERE u.activo = 1 AND u.rol = 'empleado' AND (
      (u.nombre = 'Mariangel' AND c.nombre IN ('Depilación', 'Tratamientos Faciales', 'Tratamientos Corporales'))
   OR (u.nombre = 'Rosario'   AND c.nombre = 'Manicuría')
   OR (u.nombre = 'Lucia'     AND c.nombre IN ('Cejas y Pestañas', 'Tratamientos Faciales'))
   OR (u.nombre = 'Nathalia'  AND c.nombre = 'Peluquería')
   OR (u.nombre = 'Naila'     AND c.nombre IN ('Cejas y Pestañas', 'Extensiones de Pestanas'))
);

-- Naila: solo miercoles (segun docs/negocio)
DELETE h FROM horarios h JOIN usuarios u ON u.id = h.id_empleado WHERE u.nombre = 'Naila' AND u.rol = 'empleado';
INSERT INTO horarios (id_empleado, dia_semana, hora_inicio, hora_fin, activo)
SELECT id, 3, '08:00', '20:00', 1 FROM usuarios WHERE nombre = 'Naila' AND rol = 'empleado';

INSERT INTO usuarios (nombre, apellidos, email, password, telefono, telefono_norm, rol) VALUES
 ('Carla', 'Demo', 'carla@demo.local', '', '3624 111111', '3624111111', 'cliente'),
 ('Sofia', 'Demo', 'sofia@demo.local', '', '3624 222222', '3624222222', 'cliente');

SET @dia := DATE_ADD(CURDATE(), INTERVAL (CASE DAYOFWEEK(CURDATE() + INTERVAL 1 DAY) WHEN 1 THEN 2 ELSE 1 END) DAY);

-- Confirmada con profesional (manicuria con Rosario)
INSERT INTO citas (id_cliente, id_servicio, id_empleado, fecha, hora_inicio, hora_fin, estado, notas)
SELECT (SELECT id FROM usuarios WHERE email = 'carla@demo.local'), s.id,
       (SELECT id FROM usuarios WHERE nombre = 'Rosario' AND rol = 'empleado'),
       @dia, '10:00', ADDTIME('10:00', SEC_TO_TIME(s.duracion_minutos * 60)), 'confirmada', 'demo-v2'
FROM servicios s JOIN categorias_servicios c ON c.id = s.id_categoria
WHERE c.nombre = 'Manicuría' AND s.activo = 1 ORDER BY s.id LIMIT 1;

-- Pendiente sin asignar (facial), para probar "Abrir WhatsApp" / "Marcar confirmado"
INSERT INTO citas (id_cliente, id_servicio, id_empleado, fecha, hora_inicio, hora_fin, estado, notas)
SELECT (SELECT id FROM usuarios WHERE email = 'sofia@demo.local'), s.id, NULL,
       @dia, '11:00', ADDTIME('11:00', SEC_TO_TIME(s.duracion_minutos * 60)), 'pendiente', 'demo-v2'
FROM servicios s JOIN categorias_servicios c ON c.id = s.id_categoria
WHERE c.nombre = 'Tratamientos Faciales' AND s.activo = 1 ORDER BY s.id LIMIT 1;

-- =============================================
-- LIMPIEZA (cuando se carguen los datos reales):
-- DELETE FROM citas WHERE notas = 'demo-v2';
-- DELETE FROM usuarios WHERE email LIKE '%@demo.local';
-- DELETE FROM empleados_servicios;   -- y cargar las asignaciones reales desde el panel
-- DELETE h FROM horarios h JOIN usuarios u ON u.id = h.id_empleado WHERE u.nombre = 'Naila';
-- =============================================
