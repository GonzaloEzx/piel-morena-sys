-- =============================================
-- PIEL MORENA - Datos temporales de prueba (full)
-- =============================================
-- Importar con la DB ya seleccionada en phpMyAdmin
-- Recomendado en entorno de prueba/staging

START TRANSACTION;

-- -------------------------------------------------
-- 1) Usuarios base de prueba (idempotente)
-- Password para todos: asdasd
-- -------------------------------------------------
INSERT INTO usuarios (nombre, apellidos, email, password, telefono, rol, activo)
VALUES
('Admin', 'Piel Morena', 'admin@pielmorena.com', '$2y$12$jeaHhERRX6wqSlSGNiHwKep880ck4lSaWw4ZXIxIFdwRhqdQT4EEC', '+54 11 1111 1111', 'admin', 1),
('Valeria', 'Ramos', 'caja@pielmorena.com', '$2y$12$jeaHhERRX6wqSlSGNiHwKep880ck4lSaWw4ZXIxIFdwRhqdQT4EEC', '+54 11 2222 2222', 'empleado', 1),
('Lucia', 'Mendez', 'recepcion@pielmorena.com', '$2y$12$jeaHhERRX6wqSlSGNiHwKep880ck4lSaWw4ZXIxIFdwRhqdQT4EEC', '+54 11 3333 3333', 'empleado', 1),
('Carla', 'Gomez', 'cliente1@pielmorena.com', '$2y$12$jeaHhERRX6wqSlSGNiHwKep880ck4lSaWw4ZXIxIFdwRhqdQT4EEC', '+54 11 4444 4441', 'cliente', 1),
('Rocio', 'Perez', 'cliente2@pielmorena.com', '$2y$12$jeaHhERRX6wqSlSGNiHwKep880ck4lSaWw4ZXIxIFdwRhqdQT4EEC', '+54 11 4444 4442', 'cliente', 1),
('Milagros', 'Diaz', 'cliente3@pielmorena.com', '$2y$12$jeaHhERRX6wqSlSGNiHwKep880ck4lSaWw4ZXIxIFdwRhqdQT4EEC', '+54 11 4444 4443', 'cliente', 1),
('Sofia', 'Lopez', 'cliente4@pielmorena.com', '$2y$12$jeaHhERRX6wqSlSGNiHwKep880ck4lSaWw4ZXIxIFdwRhqdQT4EEC', '+54 11 4444 4444', 'cliente', 1)
ON DUPLICATE KEY UPDATE
  nombre = VALUES(nombre),
  apellidos = VALUES(apellidos),
  password = VALUES(password),
  telefono = VALUES(telefono),
  rol = VALUES(rol),
  activo = VALUES(activo),
  updated_at = CURRENT_TIMESTAMP;

-- IDs de trabajo
SET @admin_id      = (SELECT id FROM usuarios WHERE email = 'admin@pielmorena.com' LIMIT 1);
SET @emp1_id       = (SELECT id FROM usuarios WHERE email = 'caja@pielmorena.com' LIMIT 1);
SET @emp2_id       = (SELECT id FROM usuarios WHERE email = 'recepcion@pielmorena.com' LIMIT 1);
SET @cli1_id       = (SELECT id FROM usuarios WHERE email = 'cliente1@pielmorena.com' LIMIT 1);
SET @cli2_id       = (SELECT id FROM usuarios WHERE email = 'cliente2@pielmorena.com' LIMIT 1);
SET @cli3_id       = (SELECT id FROM usuarios WHERE email = 'cliente3@pielmorena.com' LIMIT 1);
SET @cli4_id       = (SELECT id FROM usuarios WHERE email = 'cliente4@pielmorena.com' LIMIT 1);

-- -------------------------------------------------
-- 2) Categorias y servicios de prueba
-- -------------------------------------------------
INSERT INTO categorias_servicios (nombre, descripcion, icono, orden, activo)
SELECT 'Depilacion', 'Servicios de depilacion', 'bi-stars', 1, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM categorias_servicios WHERE nombre = 'Depilacion');

INSERT INTO categorias_servicios (nombre, descripcion, icono, orden, activo)
SELECT 'Facial', 'Tratamientos faciales', 'bi-droplet-half', 2, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM categorias_servicios WHERE nombre = 'Facial');

INSERT INTO categorias_servicios (nombre, descripcion, icono, orden, activo)
SELECT 'Corporal', 'Tratamientos corporales', 'bi-person-arms-up', 3, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM categorias_servicios WHERE nombre = 'Corporal');

SET @cat_dep = (SELECT id FROM categorias_servicios WHERE nombre IN ('Depilacion', 'Depilación') ORDER BY id LIMIT 1);
SET @cat_fac = (SELECT id FROM categorias_servicios WHERE nombre IN ('Facial', 'Tratamientos Faciales') ORDER BY id LIMIT 1);
SET @cat_cor = (SELECT id FROM categorias_servicios WHERE nombre IN ('Corporal', 'Tratamientos Corporales') ORDER BY id LIMIT 1);

INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion_minutos, activo)
SELECT @cat_dep, 'Depilacion Laser Axilas', 'Sesion rapida de depilacion laser', 350.00, 30, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Depilacion Laser Axilas');

INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion_minutos, activo)
SELECT @cat_fac, 'Limpieza Facial Profunda', 'Limpieza con extraccion y mascarilla', 450.00, 60, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Limpieza Facial Profunda');

INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion_minutos, activo)
SELECT @cat_cor, 'Masaje Relajante', 'Masaje corporal con aceites', 500.00, 60, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Masaje Relajante');

INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion_minutos, activo)
SELECT @cat_fac, 'Hydra Glow Facial', 'Hidratacion premium para rostro', 650.00, 50, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Hydra Glow Facial');

SET @srv_dep = (SELECT id FROM servicios WHERE nombre = 'Depilacion Laser Axilas' LIMIT 1);
SET @srv_fac = (SELECT id FROM servicios WHERE nombre = 'Limpieza Facial Profunda' LIMIT 1);
SET @srv_mas = (SELECT id FROM servicios WHERE nombre = 'Masaje Relajante' LIMIT 1);
SET @srv_hyd = (SELECT id FROM servicios WHERE nombre = 'Hydra Glow Facial' LIMIT 1);

-- -------------------------------------------------
-- 3) Relacion empleados-servicios y horarios
-- -------------------------------------------------
INSERT IGNORE INTO empleados_servicios (id_empleado, id_servicio)
VALUES
(@emp1_id, @srv_dep),
(@emp1_id, @srv_fac),
(@emp2_id, @srv_mas),
(@emp2_id, @srv_hyd);

-- Horarios semanales base (1=lunes, 7=domingo)
INSERT INTO horarios (id_empleado, dia_semana, hora_inicio, hora_fin, activo)
SELECT @emp1_id, 1, '09:00:00', '18:00:00', 1 FROM DUAL
WHERE @emp1_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM horarios WHERE id_empleado=@emp1_id AND dia_semana=1 AND hora_inicio='09:00:00');
INSERT INTO horarios (id_empleado, dia_semana, hora_inicio, hora_fin, activo)
SELECT @emp1_id, 2, '09:00:00', '18:00:00', 1 FROM DUAL
WHERE @emp1_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM horarios WHERE id_empleado=@emp1_id AND dia_semana=2 AND hora_inicio='09:00:00');
INSERT INTO horarios (id_empleado, dia_semana, hora_inicio, hora_fin, activo)
SELECT @emp1_id, 3, '09:00:00', '18:00:00', 1 FROM DUAL
WHERE @emp1_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM horarios WHERE id_empleado=@emp1_id AND dia_semana=3 AND hora_inicio='09:00:00');
INSERT INTO horarios (id_empleado, dia_semana, hora_inicio, hora_fin, activo)
SELECT @emp2_id, 4, '10:00:00', '19:00:00', 1 FROM DUAL
WHERE @emp2_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM horarios WHERE id_empleado=@emp2_id AND dia_semana=4 AND hora_inicio='10:00:00');
INSERT INTO horarios (id_empleado, dia_semana, hora_inicio, hora_fin, activo)
SELECT @emp2_id, 5, '10:00:00', '19:00:00', 1 FROM DUAL
WHERE @emp2_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM horarios WHERE id_empleado=@emp2_id AND dia_semana=5 AND hora_inicio='10:00:00');
INSERT INTO horarios (id_empleado, dia_semana, hora_inicio, hora_fin, activo)
SELECT @emp2_id, 6, '09:00:00', '14:00:00', 1 FROM DUAL
WHERE @emp2_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM horarios WHERE id_empleado=@emp2_id AND dia_semana=6 AND hora_inicio='09:00:00');

-- -------------------------------------------------
-- 4) Productos
-- -------------------------------------------------
INSERT INTO productos (nombre, descripcion, precio, stock, stock_minimo, activo)
SELECT 'Serum Vitamina C', 'Uso facial diario', 199.00, 25, 5, 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE nombre='Serum Vitamina C');

INSERT INTO productos (nombre, descripcion, precio, stock, stock_minimo, activo)
SELECT 'Crema Hidratante PM', 'Hidratante piel normal a seca', 259.00, 18, 4, 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE nombre='Crema Hidratante PM');

INSERT INTO productos (nombre, descripcion, precio, stock, stock_minimo, activo)
SELECT 'Protector Solar FPS50', 'Proteccion diaria', 229.00, 30, 6, 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE nombre='Protector Solar FPS50');

-- -------------------------------------------------
-- 5) Citas de prueba (pasadas y futuras)
-- -------------------------------------------------
INSERT INTO citas (id_cliente, id_servicio, id_empleado, fecha, hora_inicio, hora_fin, estado, notas)
SELECT @cli1_id, @srv_dep, @emp1_id, CURDATE() + INTERVAL 1 DAY, '10:00:00', '10:30:00', 'pendiente', 'Primera sesion'
FROM DUAL
WHERE @cli1_id IS NOT NULL AND @srv_dep IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM citas
    WHERE id_cliente=@cli1_id AND fecha=CURDATE() + INTERVAL 1 DAY AND hora_inicio='10:00:00'
  );

INSERT INTO citas (id_cliente, id_servicio, id_empleado, fecha, hora_inicio, hora_fin, estado, notas)
SELECT @cli2_id, @srv_fac, @emp1_id, CURDATE() + INTERVAL 2 DAY, '11:00:00', '12:00:00', 'confirmada', 'Limpieza completa'
FROM DUAL
WHERE @cli2_id IS NOT NULL AND @srv_fac IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM citas
    WHERE id_cliente=@cli2_id AND fecha=CURDATE() + INTERVAL 2 DAY AND hora_inicio='11:00:00'
  );

INSERT INTO citas (id_cliente, id_servicio, id_empleado, fecha, hora_inicio, hora_fin, estado, notas)
SELECT @cli3_id, @srv_mas, @emp2_id, CURDATE() - INTERVAL 1 DAY, '16:00:00', '17:00:00', 'completada', 'Sesion relajante'
FROM DUAL
WHERE @cli3_id IS NOT NULL AND @srv_mas IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM citas
    WHERE id_cliente=@cli3_id AND fecha=CURDATE() - INTERVAL 1 DAY AND hora_inicio='16:00:00'
  );

INSERT INTO citas (id_cliente, id_servicio, id_empleado, fecha, hora_inicio, hora_fin, estado, notas)
SELECT @cli4_id, @srv_hyd, @emp2_id, CURDATE() + INTERVAL 3 DAY, '12:00:00', '12:50:00', 'cancelada', 'Reagenda proxima semana'
FROM DUAL
WHERE @cli4_id IS NOT NULL AND @srv_hyd IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM citas
    WHERE id_cliente=@cli4_id AND fecha=CURDATE() + INTERVAL 3 DAY AND hora_inicio='12:00:00'
  );

SET @cita_ok = (
  SELECT id FROM citas
  WHERE id_cliente=@cli2_id AND fecha=CURDATE() + INTERVAL 2 DAY AND hora_inicio='11:00:00'
  LIMIT 1
);

-- -------------------------------------------------
-- 6) Caja y cierres
-- -------------------------------------------------
INSERT INTO caja_movimientos (tipo, monto, concepto, metodo_pago, id_cita, id_usuario, fecha)
SELECT 'entrada', 450.00, 'Cobro limpieza facial', 'transferencia', @cita_ok, @admin_id, CURDATE()
FROM DUAL
WHERE @admin_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM caja_movimientos
    WHERE concepto='Cobro limpieza facial' AND fecha=CURDATE()
  );

INSERT INTO caja_movimientos (tipo, monto, concepto, metodo_pago, id_usuario, fecha)
SELECT 'salida', 120.00, 'Compra insumos', 'efectivo', @admin_id, CURDATE()
FROM DUAL
WHERE @admin_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM caja_movimientos
    WHERE concepto='Compra insumos' AND fecha=CURDATE()
  );

INSERT INTO cierres_caja (fecha, total_entradas, total_salidas, saldo, id_usuario, notas)
VALUES (CURDATE(), 450.00, 120.00, 330.00, IFNULL(@admin_id, 1), 'Cierre de prueba')
ON DUPLICATE KEY UPDATE
  total_entradas = VALUES(total_entradas),
  total_salidas = VALUES(total_salidas),
  saldo = VALUES(saldo),
  id_usuario = VALUES(id_usuario),
  notas = VALUES(notas);

-- -------------------------------------------------
-- 7) Consultas de precio y notificaciones
-- -------------------------------------------------
INSERT INTO consultas_precio (id_servicio, ip_visitante, user_agent)
SELECT @srv_dep, '190.12.10.1', 'Mozilla/5.0 Test Browser'
FROM DUAL
WHERE @srv_dep IS NOT NULL;

INSERT INTO consultas_precio (id_servicio, ip_visitante, user_agent)
SELECT @srv_fac, '190.12.10.2', 'Mozilla/5.0 Test Browser'
FROM DUAL
WHERE @srv_fac IS NOT NULL;

INSERT INTO notificaciones (id_usuario, tipo, titulo, mensaje, leida, fecha_envio)
SELECT @cli1_id, 'recordatorio_cita', 'Recordatorio de cita', 'Tu cita es manana a las 10:00', 0, NOW()
FROM DUAL
WHERE @cli1_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM notificaciones
    WHERE id_usuario=@cli1_id AND titulo='Recordatorio de cita'
  );

INSERT INTO notificaciones (id_usuario, tipo, titulo, mensaje, leida, fecha_envio)
SELECT @cli2_id, 'promocion', 'Promo facial', '10% off en Hydra Glow esta semana', 0, NOW()
FROM DUAL
WHERE @cli2_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM notificaciones
    WHERE id_usuario=@cli2_id AND titulo='Promo facial'
  );

-- -------------------------------------------------
-- 8) Promociones y mensajes de contacto
-- -------------------------------------------------
INSERT INTO promociones (titulo, descripcion, descuento_porcentaje, fecha_inicio, fecha_fin, activo)
SELECT 'Semana Glow', 'Descuento en faciales seleccionados', 10.00, CURDATE(), CURDATE() + INTERVAL 10 DAY, 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM promociones
  WHERE titulo='Semana Glow' AND fecha_inicio=CURDATE()
);

INSERT INTO promociones (titulo, descripcion, descuento_monto, fecha_inicio, fecha_fin, activo)
SELECT 'Promo Axilas', 'Precio especial depilacion laser axilas', 50.00, CURDATE(), CURDATE() + INTERVAL 15 DAY, 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM promociones
  WHERE titulo='Promo Axilas' AND fecha_inicio=CURDATE()
);

INSERT INTO contacto_mensajes (nombre, email, telefono, mensaje, leido)
SELECT 'Paula Test', 'paula.test@mail.com', '+54 11 5555 1010', 'Quiero consultar por planes faciales.', 0
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM contacto_mensajes
  WHERE email='paula.test@mail.com' AND mensaje='Quiero consultar por planes faciales.'
);

-- -------------------------------------------------
-- 9) Configuracion clave (upsert)
-- -------------------------------------------------
INSERT INTO configuracion (clave, valor, descripcion)
VALUES
('nombre_negocio', 'Piel Morena', 'Nombre del salon'),
('telefono', '+54 11 0000 0000', 'Telefono principal'),
('email', 'contacto@pielmorena.com', 'Email de contacto'),
('direccion', 'Direccion de prueba', 'Direccion fisica'),
('horario_apertura', '09:00', 'Hora de apertura'),
('horario_cierre', '20:00', 'Hora de cierre'),
('intervalo_citas', '30', 'Intervalo entre citas en minutos'),
('color_primario', '#8A7650', 'Color primario del tema'),
('color_secundario', '#FFE1AF', 'Color secundario del tema')
ON DUPLICATE KEY UPDATE
  valor = VALUES(valor),
  descripcion = VALUES(descripcion);

COMMIT;

-- Fin: datos temporales de prueba
