-- =============================================
-- Migración 008 - Normalizar testimonios a 6 slots fijos
-- =============================================

ALTER TABLE testimonios
    DROP INDEX idx_orden,
    ADD UNIQUE KEY uk_testimonios_orden (orden);

DELETE FROM testimonios;

INSERT INTO testimonios (nombre, rol, texto, orden) VALUES
('Carolina López', 'Clienta frecuente', 'Desde que empecé mis sesiones de depilación láser en Piel Morena, mi vida cambió. El trato es increíble, me siento como en casa cada vez que voy. Los resultados son visibles desde la primera sesión. ¡100% recomendado!', 1),
('Valentina Martínez', 'Clienta de crioterapia', 'Me hice la crioterapia facial y quedé fascinada. Mi piel se ve más joven, más firme y radiante. Las chicas de Piel Morena son profesionales de verdad, te explican todo el proceso y te hacen sentir segura.', 2),
('Florencia Sánchez', 'Novia 2025', 'El maquillaje de mi boda fue perfecto. Sofía entendió exactamente lo que quería y el resultado superó mis expectativas. Aguantó toda la fiesta sin retoques. Piel Morena es sinónimo de calidad y profesionalismo.', 3),
('Gonzalo', 'Cliente de depilación', 'Estoy encantado con la depilación para hombres que me hice en Piel Morena, el resultado fue excelente y el proceso fue muy cómodo. El personal fue muy amable y profesional, me hicieron sentir muy cómodo durante todo el tratamiento.', 4),
('Lucía Fernández', 'Clienta de lifting de pestañas', 'Me encantó cómo quedaron mis pestañas. El resultado fue natural, prolijo y duradero. La atención fue cálida desde el primer momento y me fui feliz con el servicio.', 5),
('María Gómez', 'Clienta de limpieza facial', 'La limpieza facial fue súper completa y delicada. Sentí la piel mucho más luminosa y fresca desde ese mismo día. Se nota el profesionalismo y el cuidado en cada detalle.', 6);
