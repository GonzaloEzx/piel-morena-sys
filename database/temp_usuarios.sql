-- =============================================
-- PIEL MORENA - Usuarios temporales de prueba
-- =============================================
-- Importar este archivo en la DB ya seleccionada (ej: u347774250_pielmorena)
-- Password para todos: asdasd

-- Credenciales de prueba:
-- admin@pielmorena.com
-- caja@pielmorena.com
-- recepcion@pielmorena.com
-- cliente1@pielmorena.com
-- cliente2@pielmorena.com
-- cliente3@pielmorena.com
-- cliente4@pielmorena.com

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
