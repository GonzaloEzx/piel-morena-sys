-- =============================================
-- Migración 007 - Crear tabla testimonios
-- =============================================

CREATE TABLE IF NOT EXISTS testimonios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    rol VARCHAR(120) DEFAULT NULL,
    texto TEXT NOT NULL,
    orden INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_orden (orden)
) ENGINE=InnoDB;

INSERT INTO testimonios (nombre, rol, texto, orden)
SELECT * FROM (
    SELECT
        'Carolina López' AS nombre,
        'Clienta frecuente' AS rol,
        'Desde que empecé mis sesiones de depilación láser en Piel Morena, mi vida cambió. El trato es increíble, me siento como en casa cada vez que voy. Los resultados son visibles desde la primera sesión. ¡100% recomendado!' AS texto,
        1 AS orden
    UNION ALL
    SELECT
        'Valentina Martínez',
        'Clienta de crioterapia',
        'Me hice la crioterapia facial y quedé fascinada. Mi piel se ve más joven, más firme y radiante. Las chicas de Piel Morena son profesionales de verdad, te explican todo el proceso y te hacen sentir segura.',
        2
    UNION ALL
    SELECT
        'Florencia Sánchez',
        'Novia 2025',
        'El maquillaje de mi boda fue perfecto. Sofía entendió exactamente lo que quería y el resultado superó mis expectativas. Aguantó toda la fiesta sin retoques. Piel Morena es sinónimo de calidad y profesionalismo.',
        3
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM testimonios LIMIT 1);
