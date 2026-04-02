/* =========================================================
   PIEL MORENA - LIMPIEZA CONTROLADA PRE-GO-LIVE
   Archivo: database/cleanup_pre_golive.sql

   Objetivo:
   - eliminar datos operativos y de prueba antes del go-live;
   - conservar usuarios internos, catalogo activo y configuracion comercial;
   - ejecutar sin desactivar restricciones referenciales.

   Supuestos operativos:
   - existe backup completo previo;
   - el sitio esta en modo mantenimiento;
   - no hay escrituras concurrentes mientras corre este script.

   Conserva:
   - usuarios con rol = 'admin'
   - usuarios con rol = 'empleado'
   - categorias_servicios
   - productos
   - promociones
   - servicios activos

   Elimina:
   - caja_movimientos
   - cierres_caja
   - consultas_precio
   - notificaciones
   - contacto_mensajes
   - codigos_verificacion
   - citas
   - servicios inactivos
   - usuarios cliente
   ========================================================= */

/* =========================
   1) PRE-CHEQUEO
   ========================= */
SELECT 'usuarios_admin' AS tabla, COUNT(*) AS cantidad FROM usuarios WHERE rol = 'admin'
UNION ALL
SELECT 'usuarios_empleado', COUNT(*) FROM usuarios WHERE rol = 'empleado'
UNION ALL
SELECT 'usuarios_cliente', COUNT(*) FROM usuarios WHERE rol = 'cliente'
UNION ALL
SELECT 'citas', COUNT(*) FROM citas
UNION ALL
SELECT 'caja_movimientos', COUNT(*) FROM caja_movimientos
UNION ALL
SELECT 'cierres_caja', COUNT(*) FROM cierres_caja
UNION ALL
SELECT 'consultas_precio', COUNT(*) FROM consultas_precio
UNION ALL
SELECT 'notificaciones', COUNT(*) FROM notificaciones
UNION ALL
SELECT 'contacto_mensajes', COUNT(*) FROM contacto_mensajes
UNION ALL
SELECT 'codigos_verificacion', COUNT(*) FROM codigos_verificacion
UNION ALL
SELECT 'servicios_inactivos', COUNT(*) FROM servicios WHERE activo = 0
UNION ALL
SELECT 'servicios_activos', COUNT(*) FROM servicios WHERE activo = 1;

/* =========================
   2) LIMPIEZA OPERATIVA
   ========================= */
START TRANSACTION;

/* 2.1 Tablas derivadas y temporales */
DELETE FROM caja_movimientos;
DELETE FROM cierres_caja;
DELETE FROM consultas_precio;
DELETE FROM notificaciones;
DELETE FROM contacto_mensajes;
DELETE FROM codigos_verificacion;

/* 2.2 Historial de turnos */
DELETE FROM citas;

/* 2.3 Catalogo residual no activo
   Nota: empleados_servicios asociados se eliminan por ON DELETE CASCADE.
*/
DELETE FROM servicios
WHERE activo = 0;

/* 2.4 Clientes de prueba
   Nota: si por error quedara alguna cita, se elimina por ON DELETE CASCADE.
*/
DELETE FROM usuarios
WHERE rol = 'cliente';

COMMIT;

/* =========================
   3) RESET CONDICIONAL DE AUTO_INCREMENT
   Solo si la tabla quedo vacia
   ========================= */

SET @count_rows = (SELECT COUNT(*) FROM citas);
SET @sql_stmt = IF(@count_rows = 0, 'ALTER TABLE citas AUTO_INCREMENT = 1', 'SELECT ''AUTO_INCREMENT citas unchanged''');
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @count_rows = (SELECT COUNT(*) FROM caja_movimientos);
SET @sql_stmt = IF(@count_rows = 0, 'ALTER TABLE caja_movimientos AUTO_INCREMENT = 1', 'SELECT ''AUTO_INCREMENT caja_movimientos unchanged''');
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @count_rows = (SELECT COUNT(*) FROM cierres_caja);
SET @sql_stmt = IF(@count_rows = 0, 'ALTER TABLE cierres_caja AUTO_INCREMENT = 1', 'SELECT ''AUTO_INCREMENT cierres_caja unchanged''');
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @count_rows = (SELECT COUNT(*) FROM consultas_precio);
SET @sql_stmt = IF(@count_rows = 0, 'ALTER TABLE consultas_precio AUTO_INCREMENT = 1', 'SELECT ''AUTO_INCREMENT consultas_precio unchanged''');
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @count_rows = (SELECT COUNT(*) FROM notificaciones);
SET @sql_stmt = IF(@count_rows = 0, 'ALTER TABLE notificaciones AUTO_INCREMENT = 1', 'SELECT ''AUTO_INCREMENT notificaciones unchanged''');
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @count_rows = (SELECT COUNT(*) FROM contacto_mensajes);
SET @sql_stmt = IF(@count_rows = 0, 'ALTER TABLE contacto_mensajes AUTO_INCREMENT = 1', 'SELECT ''AUTO_INCREMENT contacto_mensajes unchanged''');
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @count_rows = (SELECT COUNT(*) FROM codigos_verificacion);
SET @sql_stmt = IF(@count_rows = 0, 'ALTER TABLE codigos_verificacion AUTO_INCREMENT = 1', 'SELECT ''AUTO_INCREMENT codigos_verificacion unchanged''');
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

/* Limpieza de variables */
SET @count_rows = NULL;
SET @sql_stmt = NULL;

/* =========================
   4) POST-CHEQUEO
   ========================= */
SELECT 'usuarios_admin' AS tabla, COUNT(*) AS cantidad FROM usuarios WHERE rol = 'admin'
UNION ALL
SELECT 'usuarios_empleado', COUNT(*) FROM usuarios WHERE rol = 'empleado'
UNION ALL
SELECT 'usuarios_cliente', COUNT(*) FROM usuarios WHERE rol = 'cliente'
UNION ALL
SELECT 'citas', COUNT(*) FROM citas
UNION ALL
SELECT 'caja_movimientos', COUNT(*) FROM caja_movimientos
UNION ALL
SELECT 'cierres_caja', COUNT(*) FROM cierres_caja
UNION ALL
SELECT 'consultas_precio', COUNT(*) FROM consultas_precio
UNION ALL
SELECT 'notificaciones', COUNT(*) FROM notificaciones
UNION ALL
SELECT 'contacto_mensajes', COUNT(*) FROM contacto_mensajes
UNION ALL
SELECT 'codigos_verificacion', COUNT(*) FROM codigos_verificacion
UNION ALL
SELECT 'servicios_inactivos_restantes', COUNT(*) FROM servicios WHERE activo = 0
UNION ALL
SELECT 'servicios_activos_restantes', COUNT(*) FROM servicios WHERE activo = 1;

/* =========================
   5) VALIDACION MANUAL FINAL
   ========================= */
SELECT id, nombre, apellidos, email, rol, activo
FROM usuarios
ORDER BY
    CASE rol
        WHEN 'admin' THEN 1
        WHEN 'empleado' THEN 2
        WHEN 'cliente' THEN 3
        ELSE 4
    END,
    id;

SELECT id, nombre, activo
FROM servicios
ORDER BY id;
