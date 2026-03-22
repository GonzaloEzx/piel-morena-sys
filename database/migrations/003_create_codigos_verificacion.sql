-- Migración: Tabla para códigos de verificación de email y recuperación de contraseña
CREATE TABLE IF NOT EXISTS codigos_verificacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    codigo VARCHAR(10) NOT NULL,
    tipo ENUM('registro', 'recuperacion') NOT NULL,
    intentos TINYINT NOT NULL DEFAULT 0,
    usado TINYINT(1) NOT NULL DEFAULT 0,
    expira_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email_tipo (email, tipo),
    INDEX idx_expira (expira_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
