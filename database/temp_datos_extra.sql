-- =============================================
-- PIEL MORENA - Datos extra de prueba (sin usuarios)
-- =============================================
-- Uso recomendado:
-- 1) Importar primero: database/temp_usuarios.sql
-- 2) Importar este archivo en la misma DB seleccionada

START TRANSACTION;

-- -------------------------------------------------
-- IDs base (prioriza emails de temp_usuarios.sql)
-- -------------------------------------------------
SET @admin_id = COALESCE(
  (SELECT id FROM usuarios WHERE email='admin@pielmorena.com' LIMIT 1),
  (SELECT id FROM usuarios WHERE rol='admin' ORDER BY id LIMIT 1)
);
SET @emp1_id = COALESCE(
  (SELECT id FROM usuarios WHERE email='caja@pielmorena.com' LIMIT 1),
  (SELECT id FROM usuarios WHERE rol='empleado' ORDER BY id LIMIT 1)
);
SET @emp2_id = COALESCE(
  (SELECT id FROM usuarios WHERE email='recepcion@pielmorena.com' LIMIT 1),
  (SELECT id FROM usuarios WHERE rol='empleado' ORDER BY id DESC LIMIT 1)
);
SET @cli1_id = COALESCE(
  (SELECT id FROM usuarios WHERE email='cliente1@pielmorena.com' LIMIT 1),
  (SELECT id FROM usuarios WHERE rol='cliente' ORDER BY id LIMIT 1)
);
SET @cli2_id = COALESCE(
  (SELECT id FROM usuarios WHERE email='cliente2@pielmorena.com' LIMIT 1),
  (SELECT id FROM usuarios WHERE rol='cliente' ORDER BY id DESC LIMIT 1)
);

-- -------------------------------------------------
-- 1) Categorias y servicios
-- -------------------------------------------------
INSERT INTO categorias_servicios (nombre, descripcion, icono, orden, activo)
SELECT 'Depilacion', 'Servicios de depilacion', 'bi-stars', 1, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM categorias_servicios WHERE nombre='Depilacion');

INSERT INTO categorias_servicios (nombre, descripcion, icono, orden, activo)
SELECT 'Facial', 'Tratamientos faciales', 'bi-droplet-half', 2, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM categorias_servicios WHERE nombre='Facial');

INSERT INTO categorias_servicios (nombre, descripcion, icono, orden, activo)
SELECT 'Corporal', 'Tratamientos corporales', 'bi-person-arms-up', 3, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM categorias_servicios WHERE nombre='Corporal');

SET @cat_dep = (SELECT id FROM categorias_servicios WHERE nombre='Depilacion' LIMIT 1);
SET @cat_fac = (SELECT id FROM categorias_servicios WHERE nombre='Facial' LIMIT 1);
SET @cat_cor = (SELECT id FROM categorias_servicios WHERE nombre='Corporal' LIMIT 1);

INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion_minutos, activo)
SELECT @cat_dep, 'Depilacion Laser Axilas', 'Sesion rapida de depilacion laser', 350.00, 30, 1
FROM DUAL
WHERE @cat_dep IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM servicios WHERE nombre='Depilacion Laser Axilas');

INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion_minutos, activo)
SELECT @cat_fac, 'Limpieza Facial Profunda', 'Limpieza con extraccion y mascarilla', 450.00, 60, 1
FROM DUAL
WHERE @cat_fac IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM servicios WHERE nombre='Limpieza Facial Profunda');

INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion_minutos, activo)
SELECT @cat_cor, 'Masaje Relajante', 'Masaje corporal con aceites', 500.00, 60, 1
FROM DUAL
WHERE @cat_cor IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM servicios WHERE nombre='Masaje Relajante');

SET @srv_dep = (SELECT id FROM servicios WHERE nombre='Depilacion Laser Axilas' LIMIT 1);
SET @srv_fac = (SELECT id FROM servicios WHERE nombre='Limpieza Facial Profunda' LIMIT 1);
SET @srv_mas = (SELECT id FROM servicios WHERE nombre='Masaje Relajante' LIMIT 1);

-- -------------------------------------------------
-- 2) Empleados/servicios y horarios
-- -------------------------------------------------
INSERT IGNORE INTO empleados_servicios (id_empleado, id_servicio)
VALUES
(@emp1_id, @srv_dep),
(@emp1_id, @srv_fac),
(@emp2_id, @srv_mas);

INSERT INTO horarios (id_empleado, dia_semana, hora_inicio, hora_fin, activo)
SELECT @emp1_id, 1, '09:00:00', '18:00:00', 1 FROM DUAL
WHERE @emp1_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM horarios WHERE id_empleado=@emp1_id AND dia_semana=1 AND hora_inicio='09:00:00');

INSERT INTO horarios (id_empleado, dia_semana, hora_inicio, hora_fin, activo)
SELECT @emp2_id, 2, '10:00:00', '19:00:00', 1 FROM DUAL
WHERE @emp2_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM horarios WHERE id_empleado=@emp2_id AND dia_semana=2 AND hora_inicio='10:00:00');

-- -------------------------------------------------
-- 3) Productos
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
-- 4) Citas
-- -------------------------------------------------
INSERT INTO citas (id_cliente, id_servicio, id_empleado, fecha, hora_inicio, hora_fin, estado, notas)
SELECT @cli1_id, @srv_dep, @emp1_id, CURDATE() + INTERVAL 1 DAY, '10:00:00', '10:30:00', 'pendiente', 'Primera sesion'
FROM DUAL
WHERE @cli1_id IS NOT NULL AND @srv_dep IS NOT NULL AND @emp1_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM citas
    WHERE id_cliente=@cli1_id AND fecha=CURDATE() + INTERVAL 1 DAY AND hora_inicio='10:00:00'
  );

INSERT INTO citas (id_cliente, id_servicio, id_empleado, fecha, hora_inicio, hora_fin, estado, notas)
SELECT @cli2_id, @srv_fac, @emp1_id, CURDATE() + INTERVAL 2 DAY, '11:00:00', '12:00:00', 'confirmada', 'Limpieza completa'
FROM DUAL
WHERE @cli2_id IS NOT NULL AND @srv_fac IS NOT NULL AND @emp1_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM citas
    WHERE id_cliente=@cli2_id AND fecha=CURDATE() + INTERVAL 2 DAY AND hora_inicio='11:00:00'
  );

SET @cita_ok = (
  SELECT id FROM citas
  WHERE id_cliente=@cli2_id AND fecha=CURDATE() + INTERVAL 2 DAY AND hora_inicio='11:00:00'
  LIMIT 1
);

-- -------------------------------------------------
-- 5) Caja + analitica + notificaciones
-- -------------------------------------------------
INSERT INTO caja_movimientos (tipo, monto, concepto, metodo_pago, id_cita, id_usuario, fecha)
SELECT 'entrada', 450.00, 'Cobro limpieza facial', 'transferencia', @cita_ok, @admin_id, CURDATE()
FROM DUAL
WHERE @admin_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM caja_movimientos WHERE concepto='Cobro limpieza facial' AND fecha=CURDATE()
  );

INSERT INTO cierres_caja (fecha, total_entradas, total_salidas, saldo, id_usuario, notas)
VALUES (CURDATE(), 450.00, 0.00, 450.00, IFNULL(@admin_id, 1), 'Cierre de prueba')
ON DUPLICATE KEY UPDATE
  total_entradas=VALUES(total_entradas),
  total_salidas=VALUES(total_salidas),
  saldo=VALUES(saldo),
  id_usuario=VALUES(id_usuario),
  notas=VALUES(notas);

INSERT INTO consultas_precio (id_servicio, ip_visitante, user_agent)
SELECT @srv_dep, '190.12.10.1', 'Mozilla/5.0 Test Browser'
FROM DUAL
WHERE @srv_dep IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM consultas_precio
    WHERE id_servicio=@srv_dep AND ip_visitante='190.12.10.1' AND DATE(created_at)=CURDATE()
  );

INSERT INTO notificaciones (id_usuario, tipo, titulo, mensaje, leida, fecha_envio)
SELECT @cli1_id, 'recordatorio_cita', 'Recordatorio de cita', 'Tu cita es manana a las 10:00', 0, NOW()
FROM DUAL
WHERE @cli1_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM notificaciones
    WHERE id_usuario=@cli1_id AND titulo='Recordatorio de cita'
  );

-- -------------------------------------------------
-- 6) Promociones + contacto + configuracion
-- -------------------------------------------------
INSERT INTO promociones (titulo, descripcion, descuento_porcentaje, fecha_inicio, fecha_fin, activo)
SELECT 'Semana Glow', 'Descuento en faciales seleccionados', 10.00, CURDATE(), CURDATE() + INTERVAL 10 DAY, 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM promociones WHERE titulo='Semana Glow' AND fecha_inicio=CURDATE()
);

INSERT INTO contacto_mensajes (nombre, email, telefono, mensaje, leido)
SELECT 'Paula Test', 'paula.test@mail.com', '+54 11 5555 1010', 'Quiero consultar por planes faciales.', 0
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM contacto_mensajes
  WHERE email='paula.test@mail.com' AND mensaje='Quiero consultar por planes faciales.'
);

INSERT INTO configuracion (clave, valor, descripcion)
VALUES
('color_primario', '#8A7650', 'Color primario del tema'),
('color_secundario', '#FFE1AF', 'Color secundario del tema'),
('intervalo_citas', '30', 'Intervalo entre citas en minutos')
ON DUPLICATE KEY UPDATE
  valor=VALUES(valor),
  descripcion=VALUES(descripcion);

COMMIT;

-- Fin: datos extra de prueba
